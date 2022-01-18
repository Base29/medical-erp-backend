<?php
/**
 * Custom Validation class
 *
 * This class can be used to return custom error messages in response against the rules. This class works in conjuction with the default Laravel Validator
 */
namespace App\Helpers;

use App\Helpers\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class CustomValidationService
{
    // Building response object
    public static function error_messages($rules, $validator)
    {
        foreach ($rules as $key => $value) {

            $hasAsterisk = Str::contains($key, '*');

            Str::replace('*', '0', $key);

            $errors = $validator->errors();

            if (Arr::has($errors->messages(), $key)) {
                return Response::fail([
                    'message' => $errors->messages()[$key][0],
                    'code' => 422,
                ]);
            }
        }
    }
}