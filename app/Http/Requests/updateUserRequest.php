<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class updateUserRequest extends FormRequest
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
            "fullname" => ["String"],
            "username" => ["String"],
            "email" => ["email"],
            "phone_number" => ["String", "unique:users,phone_number"],
            "user_type" => ["number"],
            "password" => ["String"],
            "profile_pic" => ["image", "mimes:jpeg,png,jpg,gif,svg", "max:2048"]
        ];
    }
}
