<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */

    public static function collection($data)
    {
        /* is_a() makes sure that you don't just match AbstractPaginator
         * instances but also match anything that extends that class.
         */
        if (is_a($data, \Illuminate\Pagination\AbstractPaginator::class)) {
            $data->setCollection(
                $data->getCollection()->map(function ($listing) {
                    return new static($listing);
                })
            );

            return $data;
        }

        return parent::collection($data);
    }

    public function toArray($request)
    {
        return [
            "id" => $this->id,
            "status" => $this->status,
            "track_id" => $this->track_id,
            "wallet_address" => $this->wallet_address,
            "coin_id" => $this->coin_id,
            "coin_symbol" => $this->coin_symbol,
            "received_at" => $this->received_at,
            "amount_received" => $this->amount_received,
            "amount_in_usd" => $this->amount_in_usd,
            "amount_in_ngn" => $this->amount_in_ngn,
            "paid_at" => $this->paid_at,
            "amount_paid" => $this->amount_paid,
            "currency_paid" => $this->currency_paid,
            "complete" => $this->complete,
            $this->mergeWhen(auth()->user() && auth()->user()->hasRole('admin'), [
                "wallet_data" => $this->api_data,
                "callback_data" => $this->callback_data,
                "transaction_data" => $this->transaction_data,
                "wallet" => $this->wallet
            ]),
            "coin" => new CoinResource($this->coin),
            "bank_account" => new BankAccountResource($this->bankAccount),
            "user" => new UserResource($this->user),
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
        ];
    }
}
