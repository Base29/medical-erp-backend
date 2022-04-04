<?php
/**
 * Custom Validation class
 *
 * This class can be used to return custom error messages in response against the rules. This class works in conjuction with the default Laravel Validator
 */
namespace App\Helpers;

use App\Helpers\Response;
use Illuminate\Support\Arr;

class CustomValidationService
{
    // Building response object
    public static function error_messages($rules, $validator)
    {
        foreach ($rules as $key => $value) {

            $errors = $validator->errors();

            // Keys
            $keys = [];

            // Extract keys
            foreach ($errors->messages() as $key => $value) {
                array_push($keys, $key);
            }

            if (Arr::has($errors->messages(), $keys[0])) {
                return Response::fail([
                    'message' => $errors->messages()[$keys[0]][0],
                    'code' => 422,
                ]);
            }
        }
    }
}