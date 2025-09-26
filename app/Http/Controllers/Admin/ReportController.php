<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\GenerateStaticReportData;
use App\Models\Order;
use App\Models\User;
use App\Models\Promo;
use App\Models\Report;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(request $request)
    {
        $report = Report::first();

        if (!$report || $report->updated_at->lt(now()->subHour())) {
            GenerateStaticReportData::dispatchSync();
            $report = Report::first();
        }
        
        $reportData = $report ? $report->toArray() : [];

        $sortBy = $request->get('sort_by', 'total_spent');
        $sortDir = $request->get('sort_dir', 'desc');
        $selectedMonth = $request->get('month', 'all');

        $customersQuery = DB::table('users')
            ->leftJoin('orders', function ($join) {
                $join->on('users.id', '=', 'orders.id_user')
                     ->whereIn('orders.status', ['In Progress', 'Ready', 'Completed']);
            })
            ->select(
                'users.id',
                'users.name',
                'users.email',
                DB::raw('COUNT(orders.id_pesanan) as orders_count'),
                DB::raw('COALESCE(SUM(orders.jumlah), 0) as orders_sum_jumlah'),
                DB::raw('COALESCE(SUM(orders.total), 0) as total_spent')
            );

        if ($selectedMonth !== 'all') {
            $customersQuery->where(DB::raw("DATE_FORMAT(orders.tanggal_pesan, '%Y-%m')"), $selectedMonth);
        }

        $customersQuery->groupBy('users.id', 'users.name', 'users.email')
                       ->orderBy($sortBy, $sortDir);

        $topCustomers = $customersQuery->paginate(10)->withQueryString();

        return view('admin.report', [
            'salesData' => collect($reportData['sales_data'] ?? []),
            'promoReportJsonData' => json_encode($reportData['promo_report_data'] ?? []),
            'userData' => collect($reportData['user_data'] ?? []),
            'topCustomers' => $topCustomers,
            'topCustomersFilterMonths' => collect($reportData['top_customers_filter_months'] ?? []),
        ]);
    }

    public function getMonthlyDetails(Request $request, $year, $month)
    {
        $details = Order::whereYear('created_at', $year)
                        ->whereMonth('created_at', $month)
                        ->whereIn('status', ['In Progress','Ready', 'Completed'])
                        ->with('user')
                        ->orderBy('created_at', 'desc')
                        ->get();

        return response()->json($details);
    }

    public function getPromoDetails($code)
    {
        $promo = Promo::where('code', $code)->first();
        if (!$promo) {
            return response()->json([]);
        }

        $orders = Order::where('promo_code', $promo->code)
                        ->with('user')
                        ->select('id_pesanan', 'id_user', 'tanggal_pesan', 'discount_amount')
                        ->get();
    
        $formattedOrders = $orders->map(function ($order) {
            return [
                'id_pesanan'      => $order->id_pesanan,
                'customer_name'   => $order->user ? $order->user->name : 'N/A',
                'tanggal_pesan'   => $order->tanggal_pesan,
                'discount_amount' => $order->discount_amount,
            ];
        });

        return response()->json($formattedOrders);
    }

    public function getUserDetails($year, $month)
    {
        $users = User::whereYear('created_at', $year)
                    ->whereMonth('created_at', $month)
                    ->orderBy('created_at', 'asc')
                    ->get(['id', 'name', 'email', 'created_at']);

        return response()->json($users);
    }

    public function getCustomerDetails($userId)
    {
        $user = User::find($userId);

        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        $orders = $user->orders()
                    ->whereIn('status', ['In Progress','Ready', 'Completed'])
                    ->orderBy('tanggal_pesan', 'desc')
                    ->get();

        return response()->json($orders);
    }
}