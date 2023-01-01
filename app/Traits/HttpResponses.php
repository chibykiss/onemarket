<?php

namespace App\Traits;

trait HttpResponses
{
    protected function success($data = null, $message = 'request was successful', $code = 200)
    {
        return response()->json([
            "status" => "success",
            "message" => $message,
            "data" => $data
        ], $code);
    }

    protected function error($data = null, $message = 'an error occured', $code = 200)
    {
        return response()->json([
            "status" => "fail",
            "message" => $message,
            "data" => $data
        ], $code);
    }
}