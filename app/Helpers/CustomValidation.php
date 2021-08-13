<?php
/**
 * Custom Validation class
 *
 * This class can be used to return custom error messages in response against the rules. This class works in conjuction with the default Laravel Validator
 */
namespace App\Helpers;

use Illuminate\Support\Arr;

class CustomValidation
{
    public static function error_messages($rules, $validator)
    {
        foreach ($rules as $key => $value) {
            $errors = $validator->errors();
            ray($errors);
            ray('Validation');

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