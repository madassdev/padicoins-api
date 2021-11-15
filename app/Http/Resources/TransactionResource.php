<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            "id" => $this->id,
            "wallet_id" => $this->wallet_id,
            "currency" => $this->wallet->coin_symbol,
            "wallet_address" => $this->wallet->address,
            "hash" => $this->hash,
            "status" => $this->status,
            "confirmations" => $this->confirmations,
            "confimed_at" => $this->confirmed_at,
            "amount_received" => $this->amount_received,
            "type" => $this->type,
            "payment_status" => $this->payment_status === "panding" ? "pending" : $this->payment_status,
            "amount_paid" => $this->amount_paid,
            "paid_at" => $this->paid_at,
            "complete" => $this->complete,
        ];
        return parent::toArray($request);
    }
}
