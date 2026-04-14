<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderNotification extends Notification
{
    use Queueable;

    private $orderDetail;

    public function __construct($orderDetails)
    {
        $this->orderDetail = $orderDetails;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Order Baru Dibuat')
            ->greeting('Halo, Admin!')
            ->line('Order baru telah dibuat oleh ' . ($this->orderDetail['user_name'] ?? 'Pengguna') . '.')
            ->line('Event: ' . ($this->orderDetail['event_name'] ?? '-'))
            ->line('Detail Order:')
            ->line('ID Order: ' . ($this->orderDetail['id_order'] ?? 'Tidak tersedia'))
            ->line('Jumlah Tiket: ' . ($this->orderDetail['quantity'] ?? 'Tidak tersedia'))
            ->line('Total Harga: Rp ' . number_format($this->orderDetail['total_price'] ?? 0, 0, ',', '.'))
            ->action('Lihat Order', url('/admin/orders/' . ($this->orderDetail['id_order'] ?? '#')))
            ->line('Terima kasih telah menggunakan layanan kami!');
    }

    public function toDatabase($notifiable)
    {
        return [
            'message' => 'Order baru dari ' . $this->orderDetail['user_name'] .
                ' untuk event "' . $this->orderDetail['event_name'] . '" sebanyak ' .
                $this->orderDetail['quantity'] . ' tiket. Total: Rp' .
                number_format($this->orderDetail['total_price'], 0, ',', '.'),
            'id_order' => $this->orderDetail['id_order'],
        ];
    }
}
