<?php

namespace App\Exceptions;

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
                return response([
                    'success' => false,
                    'message' => 'Invalid endpoint',
                ], 404);
            }
        });

        $this->renderable(function (AuthenticationException $exception, $request) {
            if ($request->expectsJson()) {
                return response([
                    'success' => false,
                    'message' => 'Expired or Invalid token',
                ], 401);
            }
        });
    }

    public function render($request, Throwable $exception)
    {
        if ($exception instanceof \Spatie\Permission\Exceptions\UnauthorizedException) {
            return response([
                'success' => false,
                'message' => 'You do not have the required authorization to perform this action',
            ], 403);
        }

        if ($exception instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {
            return response([
                'success' => false,
                'message' => 'Token has expired',
            ], 401);
        }

        if ($exception instanceof \Spatie\Permission\Exceptions\PermissionDoesNotExist) {
            return response([
                'success' => false,
                'message' => 'Permission `' . $request->permission . '` does not exist',
            ], 404);
        }

        if ($exception instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {
            return response([
                'success' => false,
                'message' => 'Token Signature could not be verified.',
            ], 401);
        }

        return parent::render($request, $exception);
    }
}