<?php

namespace App\Traits;

trait ResponseTrait
{
    public function generateResponse($success, $message, $data = [], $statusCode = 200)
    {

        return response()->json([
            'success' => $success,
            'message' => __($message),
            'data' => $data,
        ], $statusCode);
    }
}
