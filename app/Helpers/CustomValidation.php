<?php
namespace App\Helpers;

use Illuminate\Support\Arr;

class CustomValidation
{
    public static function error_messages($rules, $validator)
    {
        foreach ($rules as $key => $value) {
            ray($key);
            $errors = $validator->errors();

            // Return error messages for email
            if (Arr::has($errors->messages(), $key)) {
                return response([
                    'success' => false,
                    'message' => $errors->messages()[$key][0],
                ], 422);
            }
        }
    }
}