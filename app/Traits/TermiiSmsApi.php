<?php

namespace App\Traits;

use Illuminate\Support\Facades\Http;

trait TermiiSmsApi
{
    protected function sendSmsApp(int $to, int $pin_attempts, int $pin_time_to_live, int $pin_length, string $pin_type)
    {
        $data = [
            "api_key" => env('TERMII_KEY'),
            "phone_number" => $to,
            "pin_type" => $pin_type,
            "pin_attempts" => $pin_attempts,
            "pin_time_to_live" => $pin_time_to_live,
            "pin_length" => $pin_length
        ];

        $response = Http::post('https://api.ng.termii.com/api/sms/otp/generate',$data);
        return json_decode($response->body());
    }

    protected function sendSms(int $to, string $message_type, int $pin_attempts, int $pin_time_to_live, int $pin_length, string $pin_placeholder, string $message_text, string $from = 'fastBeep', string $channel = "dnd",)
    {
        $data = [
            "api_key" => env('TERMII_KEY'),
            "to" => $to,
            "from" => $from,
            "message_type" => $message_type,
            "channel" => $channel,
            "pin_attempts" => $pin_attempts,
            "pin_time_to_live" => $pin_time_to_live,
            "pin_length" => $pin_length,
            "pin_placeholder" => $pin_placeholder,
            "message_text" => $message_text
        ];

        $response = Http::post('https://api.ng.termii.com/api/sms/otp/send', $data);
        return json_decode($response->body());
    }

    protected function verifyToken(string $pin_id, string $pin)
    {
        $data = [
            "api_key" => env('TERMII_KEY'),
            "pin_id" => $pin_id,
            "pin" => $pin,
        ];

        $response = Http::post('https://api.ng.termii.com/api/sms/otp/verify', $data);
        return json_decode($response->body());
    }
}