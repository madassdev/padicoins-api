<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BankAccountResource extends JsonResource
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
            "bank_id" => $this->bank_id,
            "account_number" => $this->account_number,
            "account_name" => $this->account_name,
            "bank_name" => $this->bank_name,
            "active" => $this->active,
            "user" => new UserResource($this->whenLoaded('user'))
        ];
        return parent::toArray($request);
    }
}
