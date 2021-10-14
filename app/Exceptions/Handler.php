<?php

namespace App\Exceptions;

use App\Helpers\Response;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        $this->renderable(function (NotFoundHttpException $e, $request) {
            if ($request->expectsJson()) {
                return Response::fail([
                    'message' => 'Invalid endpoint',
                    'code' => 404,
                ]);
            }
        });

        $this->renderable(function (AuthenticationException $exception, $request) {
            if ($request->expectsJson()) {
                return Response::fail([
                    'message' => 'Expired or Invalid token',
                    'code' => 401,
                ]);
            }
        });
    }

    public function render($request, Throwable $exception)
    {
        if ($exception instanceof \Spatie\Permission\Exceptions\UnauthorizedException) {
            return Response::fail([
                'message' => 'You do not have the required permission to perform this action',
                'code' => 403,
            ]);
        }

        if ($exception instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {
            return Response::fail([
                'message' => 'Token has expired',
                'code' => 401,
            ]);
        }

        if ($exception instanceof \Spatie\Permission\Exceptions\PermissionDoesNotExist) {
            return Response::fail([
                'message' => 'Permission `' . $request->permission . '` does not exist',
                'code' => 404,
            ]);
        }

        if ($exception instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {
            return Response::fail([
                'message' => 'Token Signature could not be verified.',
                'code' => 401,
            ]);
        }

        return parent::render($request, $exception);
    }
}