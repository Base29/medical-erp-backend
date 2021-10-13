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
        if (!Arr::has($args, 'code')) {
            throw new ResponseException('Argument `code` is missing for the Response::success() method.');
        }

        return response(self::response_data($args, 'success'), 200);
    }

    public static function fail($args)
    {
        if (!Arr::has($args, 'message') && !Arr::has($args, 'code')) {
            throw new ResponseException('Arguments `message` and `code` are missing for the Response::send() method.');
        }

        return response(self::response_data($args, 'fail'), $args['code']);
    }

    private static function response_data($args, $type)
    {
        $response_array = [
            'success' => $type === 'success' ? true : false,
        ];

        foreach ($args as $key => $value) {
            if ($key !== 'code') {
                $response_array[$key] = $value;
            }
        }
        return $response_array;
    }
}