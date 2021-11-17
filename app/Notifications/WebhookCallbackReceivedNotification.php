<?php

namespace App\Notifications;

use App\Models\Wallet;
use App\Models\WebhookCallback;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WebhookCallbackReceivedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public $wallet;
    public $wcb;

    public function __construct(Wallet $wallet, WebhookCallback $wcb)
    {
        //
        $this->wallet = $wallet;
        $this->wcb = $wcb;
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
        $wallet = $this->wallet;
        $coin_name = strtoupper($wallet->coin->name);
        $track_id = strtoupper($wallet->track_id);
        $callback_hash = $this->wcb->hash;
        $callback_id = $this->wcb->id;

        return (new MailMessage)
            ->subject("New $coin_name Webhook Notification Received")
            ->greeting("Payment received")
            ->line("A user with email: **{$wallet->user->email}** has made a deposit.")
            ->line("**TRANSACTION DETAILS** \r\n")
            ->line("Coin: **{$wallet->coin->name}**")
            ->line("Wallet Address: **$wallet->address**")
            ->line("Track ID: **$track_id**")
            ->line("Callback Id: **$callback_id**")
            ->line("Callback hash: **$callback_hash**")
            ->action('View Transaction', route('admin.wallets.show', ["track_id" => $wallet->track_id]))
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
