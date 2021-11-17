<?php

namespace App\Notifications;

use App\Models\Wallet;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WalletReadyNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public $wallet;
    public function __construct(Wallet $wallet)
    {
        //
        $this->wallet = $wallet;
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
        $track_id = strtoupper($wallet->track_id);
        return (new MailMessage)
            ->subject('Your Padicoins wallet is ready!')
            ->line("You can now proceed to deposit your coins into the following address")
            ->line("Address: **{$wallet->address}**")
            ->line("Currency: **{$wallet->coin->symbol}**")
            ->line("Track ID: **{$track_id}**")
            ->line("Your account details are:")
            ->line("Account Number: **{$wallet->bankAccount->account_number}**")
            ->line("Account Name: **{$wallet->bankAccount->account_name}**")
            ->line("Bank Name: **{$wallet->bankAccount->bank_name}**")
            ->line('Your account will be funded once your deposit has been confirmed.')
            ->line('Thank you for using Padicoins!');
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
