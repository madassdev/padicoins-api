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
        // return [
        //     "id" => $this->id,
        //     "wallet_id" => $this->wallet_id,
        //     "hash" => $this->hash,
        //     "reference" => $this->reference,
        //     "type" => $this->type,
        //     "amount_received" => $this->amount_received,
        // ];
        return parent::toArray($request);
    }
}
