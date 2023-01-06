<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        //return parent::toArray($request);
        return [
            "id" => $this->id,
            "firstname" => $this->firstname,
            "lastname" => $this->lastname,
            "middlename" => $this->middlename,
            "nationality" => $this->nationality,
            "sex" => $this->sex,
            "marital_status" => $this->marital_status,
            "dob" => $this->date_of_birth,
            "username" => $this->username,
            "email" => $this->email,
            "phone_number" => $this->phone_number,
            "avatar" => $this->profile_pic,
            "user_type" => (int) $this->user_categories_id,
            "approved" => (int) $this->approved,
        ];
    }
}
