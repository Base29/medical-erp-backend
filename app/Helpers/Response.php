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
    public static function success($args)
    {
        return response(self::response_data($args, 'success'), 200);
    }

    public static function fail($args)
    {
        if (!Arr::has($args, 'message') || !Arr::has($args, 'code')) {
            throw new ResponseException('Arguments `message` and `code` are missing for the Response::fail() method.');
        }

        return response(self::response_data($args, 'fail'), $args['code']);
    }

    private static function response_data($args, $type)
    {
        // Setting the success key to true or false depending upon the response method called
        $response_array = [
            'success' => $type === 'success' ? true : false,
        ];

        // Iterating through the $args passed in the Response methods and adding them to the response array
        foreach ($args as $key => $value) {
            if ($key !== 'code') {
                $response_array[$key] = $value;
            }
        }
        return $response_array;
    }
}