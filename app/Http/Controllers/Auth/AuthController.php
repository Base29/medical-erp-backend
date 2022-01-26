<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\Response;
use App\Helpers\ResponseMessage;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\AuthenticateUserRequest;
use App\Http\Requests\Auth\ResetPasswordLinkRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Services\AuthService\AuthService;

class AuthController extends Controller
{
    protected $authService;

    // Constructor
    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    // Method for Authenticating the user
    public function login(AuthenticateUserRequest $request)
    {
        try {
            // Authenticate user
            $user = $this->authService->authenticate($request);

            // Return response
            return Response::success(['user' => $user]);

        } catch (\Exception $e) {

            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Method for logging out the user
    public function logout()
    {
        $this->authService->logout();
        return Response::success(['message' => ResponseMessage::logout()]);
    }

    // Method for resetting password
    public function resetPassword(ResetPasswordRequest $request)
    {
        try {
            $this->authService->resetPassword($request);

            return Response::success(['message' => ResponseMessage::passwordResetSuccess()]);

        } catch (\Exception $e) {

            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Method for generating reset password link
    public function generateResetPasswordLink(ResetPasswordLinkRequest $request)
    {
        try {
            $this->authService->resetPasswordLink($request);

            return Response::success(['message' => ResponseMessage::passwordResetLink($request->email)]);

        } catch (\Exception $e) {

            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Method for verifying user token
    public function verifyToken()
    {
        try {

            $userWithToken = $this->authService->verifyToken();

            return Response::success(['user' => $userWithToken]);

        } catch (\Exception $e) {

            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }
}