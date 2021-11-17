<?php

namespace App\Notifications;

use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TransactionSuccessNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public $transaction;
    public function __construct(Transaction $transaction)
    {
        //
        $this->transaction = $transaction;
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
        $transaction = $this->transaction;
        $wallet = $this->transaction->wallet;
        $track_id = strtoupper($wallet->track_id);
        $amount_paid = naira($transaction->amount_paid);
        return (new MailMessage)
                    ->subject('Your account has been funded!')
                    ->line('We have received your deposit and sent funds to your account.')
                    ->line("The details of the transaction are listed below:")
                    ->line("Address: **{$wallet->address}**")
                    ->line("Amount Received: **{$transaction->amount_received}**")
                    ->line("Currency: **{$wallet->coin->symbol}**")
                    // ->line("Transaction Has: **{$wallet->coin->symbol}**")
                    ->line("Track ID: **{$track_id}**")
                    ->line("Amount Paid: **{$amount_paid}**")
                    ->line("Account Number: **{$wallet->bankAccount->account_number}**")
                    ->line("Account Name: **{$wallet->bankAccount->account_name}**")
                    ->line("Bank Name: **{$wallet->bankAccount->bank_name}**")
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
