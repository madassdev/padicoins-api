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
            "wallet_id" => $this->wallet_id,
            "hash" => $this->hash,
            "reference" => $this->reference,
            "type" => $this->type,
            "status" => $this->status,
            "coin_symbol" => $this->wallet->coin_symbol,
            "amount_received" => $this->amount_received,
            "coin_to_usd_rate" => $this->coin_to_usd_rate,
            "usd_value"  =>$this->usd_value,
            "usd_to_ngn_rate" => $this->usd_to_ngn_rate,

            "ngn_value"  =>$this->ngn_value,
            "wallet" => $this->whenLoaded('wallet',new WalletResource($this->wallet)),
        ];
        return parent::toArray($request);
    }
}
