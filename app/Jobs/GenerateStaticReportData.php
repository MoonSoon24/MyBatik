<?php

namespace App\Jobs;

use App\Models\Order;
use App\Models\User;
use App\Models\Report;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class GenerateStaticReportData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
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
        
        $userData = User::select(
            DB::raw('YEAR(created_at) as year'),
            DB::raw('MONTH(created_at) as month'),
            DB::raw('COUNT(*) as total_users')
        )
        ->groupBy('year', 'month')
        ->orderBy('year', 'desc')
        ->orderBy('month', 'desc')
        ->get();

        $topCustomersFilterMonths = DB::table('orders')
            ->select(
                DB::raw("DATE_FORMAT(tanggal_pesan, '%Y-%m') as month_value"),
                DB::raw("DATE_FORMAT(tanggal_pesan, '%M %Y') as month_display")
            )
            ->whereIn('status', ['In Progress','Ready', 'Completed'])
            ->distinct()
            ->orderBy('month_value', 'desc')
            ->get();

        Report::updateOrCreate(['id' => 1], [
            'sales_data' => $salesData,
            'promo_report_data' => $promoReportData,
            'user_data' => $userData,
            'top_customers_filter_months' => $topCustomersFilterMonths,
        ]);
    }
}