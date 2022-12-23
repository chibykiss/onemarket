<?php

namespace App\Http\Requests\v1;

use Illuminate\Foundation\Http\FormRequest;

class OwnerRequest extends FormRequest
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
            'user_id' => ['required','numeric', 'unique:owners,user_id', 'exists:users,id'],
            'shop_id' => ['required','numeric','exists:shops,id'],
            'owner_served' => ['numeric','exists:owners,id'],
            'coming_from' => ['string'],
            'tenancy_receipt' => ["required", "image", "mimes:jpeg,png,jpg,gif,svg", "max:2048"],
            'reg_receipt' => ["required", "image", "mimes:jpeg,png,jpg,gif,svg", "max:2048"],
            'cert' => ["image", "mimes:jpeg,png,jpg,gif,svg", "max:2048"],
            'via' => ['required','string'],
            'guarantor' => ['required','numeric', "exists:owners,id"],  
            'known_for' => ['required','numeric'],
            'company_name' => ['required','string'],
            'guaranteed' => ['boolean'],
            'active' => ['boolean'],
        ];
    }
}
