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
    public static function send($args)
    {
        if (!Arr::has($args, 'type') && !Arr::has($args, 'code')) {
            throw new ResponseException('Arguments `type` and `code` are missing for the Response::send() method.');
        }

        return response(self::response_data($args), $args['code']);
    }

    private static function response_data($args)
    {
        $response_array = [
            'success' => $args['type'],
        ];

        foreach ($args as $key => $value) {
            if ($key !== 'code' && $key !== 'type') {
                $response_array[$key] = $value;
            }
        }
        return $response_array;
    }
}