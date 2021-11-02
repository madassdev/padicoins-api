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
    public function toArray($request)
    {
        return [
            "id" => $this->id,
            "status" => $this->status,
            "track_id" => $this->track_id,
            "wallet_address" => $this->wallet_address,
            "coin_id" => $this->coin_id,
            "coin_symbol" => $this->coin_symbol,
            "coin" => new CoinResource($this->coin),
            "bank_account" => new BankAccountResource($this->bankAccount),
            "user" => new UserResource($this->user),
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
        ];
    }
}
