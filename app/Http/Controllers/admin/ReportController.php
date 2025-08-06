<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Models\Promo;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(request $request)
    {
        $salesData = Order::select(
            DB::raw('YEAR(created_at) as year'),
            DB::raw('MONTH(created_at) as month'),
            DB::raw('SUM(total) as total_sales'),
            DB::raw('COUNT(*) as total_orders')
        )
        ->whereIn('status', ['In Progress','Ready', 'Completed'])
        ->groupBy('year', 'month')
        ->orderBy('year', 'desc')
        ->orderBy('month', 'desc')
        ->get();

        $promoReportData = [];
        $allTimePromoUsage = DB::table('promos')
            ->join('orders', 'promos.code', '=', 'orders.promo_code')
            ->select('promos.code', 'promos.type', 'promos.value', DB::raw('count(orders.id_pesanan) as usage_count'))
            ->whereNotNull('orders.promo_code')
            ->whereIn('orders.status', ['In Progress','Ready', 'Completed'])
            ->groupBy('promos.code', 'promos.type', 'promos.value')
            ->orderBy('usage_count', 'desc')
            ->get();
        $promoReportData['all'] = $allTimePromoUsage;

        $monthlyPromoUsage = DB::table('promos')
            ->join('orders', 'promos.code', '=', 'orders.promo_code')
            ->select(
                'promos.code',
                'promos.type',
                'promos.value',
                DB::raw('count(orders.id_pesanan) as usage_count'),
                DB::raw("DATE_FORMAT(orders.tanggal_pesan, '%Y-%m') as month_key")
            )
            ->whereNotNull('orders.promo_code')
            ->whereIn('orders.status', ['In Progress','Ready', 'Completed'])
            ->groupBy('month_key', 'promos.code', 'promos.type', 'promos.value')
            ->get();

        foreach ($monthlyPromoUsage as $promo) {
            $monthKey = $promo->month_key;
            if (!isset($promoReportData[$monthKey])) {
                $promoReportData[$monthKey] = [];
            }
            $promoReportData[$monthKey][] = (object)[
                'code' => $promo->code,
                'type' => $promo->type,
                'value' => $promo->value,
                'usage_count' => $promo->usage_count
            ];
        }
        $promoReportJsonData = json_encode($promoReportData);

        $userData = User::select(
            DB::raw('YEAR(created_at) as year'),
            DB::raw('MONTH(created_at) as month'),
            DB::raw('COUNT(*) as total_users')
        )
        ->groupBy('year', 'month')
        ->orderBy('year', 'desc')
        ->orderBy('month', 'desc')
        ->get();

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
            $customersQuery->where(function ($query) use ($selectedMonth) {
                $query->where(DB::raw("DATE_FORMAT(orders.tanggal_pesan, '%Y-%m')"), $selectedMonth)
                      ->orWhereNull('orders.id_pesanan');
            });
        }

        $customersQuery->groupBy('users.id', 'users.name', 'users.email')
                       ->orderBy($sortBy, $sortDir);

        $topCustomers = $customersQuery->paginate(10)->withQueryString();

        $topCustomersFilterMonths = DB::table('orders')
            ->select(
                DB::raw("DATE_FORMAT(tanggal_pesan, '%Y-%m') as month_value"), 
                DB::raw("DATE_FORMAT(tanggal_pesan, '%M %Y') as month_display")
            )
            ->whereIn('status', ['In Progress','Ready', 'Completed'])
            ->distinct()
            ->orderBy('month_value', 'desc')
            ->get();

        $monthlyTopCustomers = DB::table('users')
            ->join('orders', 'users.id', '=', 'orders.id_user')
            ->select(
                'users.id', 
                'users.name', 
                'users.email', 
                DB::raw('COUNT(orders.id_pesanan) as orders_count'), 
                DB::raw('SUM(orders.jumlah) as orders_sum_jumlah'),
                DB::raw('SUM(orders.total) as total_spent'),
                DB::raw("DATE_FORMAT(orders.tanggal_pesan, '%Y-%m') as month_key")
            )
            ->whereIn('orders.status', ['In Progress','Ready', 'Completed'])
            ->groupBy('month_key', 'users.id', 'users.name', 'users.email')
            ->orderBy('month_key', 'desc')
            ->orderBy('orders_count', 'desc')
            ->get();
        
        foreach ($monthlyTopCustomers as $customer) {
            $monthKey = $customer->month_key;
            if (!isset($topCustomersData[$monthKey])) {
                $topCustomersData[$monthKey] = [];
            }
            if (count($topCustomersData[$monthKey]) < 20) {
                 $topCustomersData[$monthKey][] = (object)[
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'email' => $customer->email,
                    'orders_count' => $customer->orders_count,
                    'orders_sum_jumlah' => $customer->orders_sum_jumlah,
                    'total_spent' => $customer->total_spent
                ];
            }
        }
        $topCustomersJsonData = json_encode($topCustomersData);

        return view('admin.report', compact(
            'salesData', 
            'promoReportJsonData',
            'userData', 
            'topCustomers',
            'topCustomersFilterMonths'
        ));
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