<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ShopRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            "shop_no" => ["required","string","unique:shops,shop_number"],
            "plaza_name" => ["required","string"],
            "shop_address" => ["required","string"],
            "owner" => ["numeric","exists:owners,id"],
            "via" => ["string"],
            "guarantor" => ["numeric", "exists:owners,id"],
            "known_for" => ["numeric"],
            "company_name" => ["string"],
            "guaranteed" => ["boolean"],
        ];
    }
}
