<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class WalletResource extends JsonResource
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
            "address" => $this->address,
            "coin_id" => $this->coin_id,
            "coin_symbol" => $this->coin_symbol,
            $this->mergeWhen(auth()->user() && auth()->user()->hasRole('admin'), [
                "balance" => $this->balance,
                "payload" => $this->payload,
                "webhook_url" => $this->webhook_url,
                "transactions" => $this->transactions,
                "public_key" => $this->public_key,
                "private_key" => $this->private_key,
                "wif" => $this->wif,
            ]),
            "coin" => new CoinResource($this->coin),
            "bank_account" => new BankAccountResource($this->bankAccount),
            "user" => new UserResource($this->user),
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
        ];
    }
}
