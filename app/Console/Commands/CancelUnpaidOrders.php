<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\Notification; // Import the Notification model
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CancelUnpaidOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:cancel-unpaid';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Find and cancel unpaid orders older than 24 hours';

    public function handle(): void
    {
        $this->info('Checking for unpaid orders to cancel...');

        $cancellationTime = now()->subHours(24);
        $cancelledCount = 0;

        $ordersToCancel = Order::where('status', 'Pending')
                                ->where('bukti_pembayaran', null)
                                ->where('created_at', '<', $cancellationTime)
                                ->get();

        if ($ordersToCancel->isEmpty()) {
            $this->info('No orders to cancel.');
            return;
        }

        foreach ($ordersToCancel as $order) {
            $order->update(['status' => 'Cancelled']);

            Notification::create([
                'user_id'  => $order->id_user,
                'order_id' => $order->id_pesanan,
                'title'    => 'Order Cancelled Automatically',
                'message'  => 'Your order has been cancelled because payment was not received within 24 hours.',
            ]);

            $cancelledCount++;
            Log::info("Order #{$order->id_pesanan} has been automatically cancelled and user notified.");
        }

        $this->info("Successfully cancelled {$cancelledCount} orders.");
    }
}
