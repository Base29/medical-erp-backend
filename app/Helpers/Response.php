<?php
/**
 * Response Class
 *
 * This is a class for custom response
 */

namespace App\Helpers;

use App\Exceptions\ResponseException;
use Illuminate\Support\Arr;

class Response
{
    // Send Success Response
    public static function success($args)
    {
        return response(self::responseData($args, 'success'), 200);
    }

    // Send Failed Response
    public static function fail($args)
    {
        // Checking if the required args (message & code) are passed where the Response::fail() method is used
        if (!Arr::has($args, 'message') || !Arr::has($args, 'code')) {
            throw new ResponseException('Arguments `message` and `code` are missing for the Response::fail() method.');
        }

        return response(self::responseData($args, 'fail'), $args['code']);
    }

    // Building response array with the fields provided
    private static function responseData($args, $type)
    {
        // Setting the success key to true or false depending upon the response method called
        $responseArray = [
            'success' => $type === 'success' ? true : false,
        ];

        // Iterating through the $args passed in the Response methods and adding them to the response array
        foreach ($args as $key => $value) {
            if ($key !== 'code') {
                $responseArray[$key] = $value;
            }
        }
        return $responseArray;
    }
}