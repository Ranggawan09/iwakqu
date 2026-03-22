<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use App\Models\Order;

class OrderStatusUpdatedNotification extends Notification
{
    use Queueable;

    public $order;
    public $status;

    public function __construct(Order $order, string $status)
    {
        $this->order = $order;
        $this->status = $status;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $statusLabels = [
            'diproses' => 'Diproses',
            'dikirim' => 'Sedang Dikirim',
            'selesai' => 'Selesai',
            'dibatalkan' => 'Dibatalkan'
        ];
        
        $label = $statusLabels[$this->status] ?? $this->status;

        return [
            'order_id' => $this->order->id,
            'message' => 'Pesanan #' . $this->order->id . ' Anda sekarang berubah status menjadi: ' . $label,
            'url' => route('orders.show', $this->order->id),
        ];
    }
}
