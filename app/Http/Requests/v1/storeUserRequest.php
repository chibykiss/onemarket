<?php

namespace App\Http\Requests\v1;

use Illuminate\Foundation\Http\FormRequest;

class storeUserRequest extends FormRequest
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
            "firstname" => ["required", "String"],
            "middlename" => ["String"],
            "lastname" => ["required", "String"],
            "nationality" => ["required", "String"],
            "sex" => ["required", "String"],
            "marital_status" => ["required", "String"],
            "dob" => ["required", "date"],
            "username" => ["required", "String"],
            "email" => ["email", "unique:users,email"],
            "phone_number" => ["required", "String", "unique:users,phone_number"],
            "password" => ["required","confirmed"],
            "profile_pic" => ["image","mimes:jpeg,png,jpg,gif,svg", "max:2048"]
        ];
    }
}
