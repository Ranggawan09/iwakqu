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
        return ['database', \NotificationChannels\WebPush\WebPushChannel::class];
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

    public function toWebPush($notifiable, $notification)
    {
        $statusLabels = [
            'diproses' => 'Diproses',
            'dikirim' => 'Sedang Dikirim',
            'selesai' => 'Selesai',
            'dibatalkan' => 'Dibatalkan'
        ];
        
        $label = $statusLabels[$this->status] ?? $this->status;

        return (new \NotificationChannels\WebPush\WebPushMessage)
            ->title('Update Status Pesanan IwakQu')
            ->icon('/logo.png')
            ->body('Pesanan #' . $this->order->id . ' Anda sekarang berubah status menjadi: ' . $label)
            ->action('Lihat Pesanan', route('orders.show', $this->order->id))
            ->data(['url' => route('orders.show', $this->order->id)]);
    }
}
