<?php

namespace App\Exceptions;

use Exception;

class ResponseException extends Exception
{
    public function render($request)
    {
        return response()->json(["success" => false, "message" => $this->getMessage(), "trace" => $this->getTrace()[0]], 500);
    }
}