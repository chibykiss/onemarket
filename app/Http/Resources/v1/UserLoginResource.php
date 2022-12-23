<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Resources\Json\JsonResource;

class UserLoginResource extends JsonResource
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
            "fullname" => $this->fullname,
            "username" => $this->username,
            "email" => $this->email,
            "phone_number" => $this->phone_number,
            "avatar" => $this->profile_pic,
            "user_type" => (int) $this->user_categories_id,
            "admin_type" => $this->admin->admin_type,
            "approved" => (int) $this->approved,
        ];
    }
}
