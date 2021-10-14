<?php
/**
 * Custom Validation class
 *
 * This class can be used to return custom error messages in response against the rules. This class works in conjuction with the default Laravel Validator
 */
namespace App\Helpers;

use App\Helpers\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;

class CustomValidation
{
    public static function validate_request($rules, $request)
    {
        // Validating params in request
        $validator = Validator::make($request->all(), $rules);

        // If validation fails
        if ($validator->fails()) {
            // Return error messages against $rules
            return self::error_messages($rules, $validator);
        }
    }

    // Building response object
    public static function error_messages($rules, $validator)
    {
        foreach ($rules as $key => $value) {
            $errors = $validator->errors();

            // Return error messages for email
            if (Arr::has($errors->messages(), $key)) {
                return Response::fail([
                    'message' => $errors->messages()[$key][0],
                    'code' => 422,
                ]);
            }
        }
    }
}