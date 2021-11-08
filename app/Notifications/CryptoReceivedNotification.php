<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CryptoReceivedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public $order;
    public function __construct(Order $order)
    {
        //
        $this->order = $order;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $order = $this->order;
        $coin = $this->order->coin;
        $coin_name = strtoupper($coin->name);
        $amount_received = $order->amount_received;
        $amount_in_usd = number_format((float) $order->amount_in_usd, 2, '.', ',');
        $amount_in_ngn = number_format((float) $order->amount_in_ngn, 2, '.', ',');
        $track_id = strtoupper($order->track_id);
        return (new MailMessage)
                    ->subject("New $coin_name deposit received!")
                    ->greeting("Payment received")
                    ->line("A user with email: **{$order->user->email}** has made a deposit.")
                    ->line("**TRANSACTION DETAILS** \r\n")
                    ->line("Coin: **{$coin->name}**")
                    ->line("Amount received: **$amount_received {$coin->symbol}**")
                    ->line("USD Equivalent: **{$amount_in_usd}USD**")
                    ->line("Naira Equivalent: **{$amount_in_ngn}NGN**")
                    ->line("Track ID: **$track_id**")
                    ->action('View Transaction', route('admin.wallets.show', ["track_id"=>$order->track_id]))
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
