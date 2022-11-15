<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ShopResource extends JsonResource
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
            "shop_id" => $this->id,
            "shop_no" => $this->shop_number,
            "plaza_name" => $this->plaza_name,
            "shop_address" => $this->shop_address,
            "owner" => $this->owner_id,
            "gotten_via" => $this->gotten_via,
            "guarantor" => $this->guarantor,
            "known_for" => $this->known_for,
            "company_name" => $this->company_name,
            "guaranteed" => (int) $this->guranteed,
            "approved" => $this->approved,
        ];
    }
}
