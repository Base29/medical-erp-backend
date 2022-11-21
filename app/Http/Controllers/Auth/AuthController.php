<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\Response;
use App\Helpers\ResponseMessage;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\AuthenticateUserRequest;
use App\Http\Requests\Auth\ResetPasswordLinkRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Services\Auth\AuthService;
use Exception;

class AuthController extends Controller
{
    // Local variable
    protected $authService;

    // Constructor
    public function __construct(AuthService $authService)
    {
        // Inject service
        $this->authService = $authService;
    }

    // Method for Authenticating the user
    public function login(AuthenticateUserRequest $request)
    {
        try {
            // Authenticate user service
            $user = $this->authService->authenticate($request);

            // Return response
            return Response::success([
                'code' => Response::HTTP_OK,
                'user' => $user,
            ]);

        } catch (Exception $e) {

            return Response::fail([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Method for logging out the user
    public function logout()
    {
        // Logout service
        $this->authService->logout();
        return Response::success([
            'code' => Response::HTTP_OK,
            'message' => ResponseMessage::logout(),
        ]);
    }

    // Method for resetting password
    public function resetPassword(ResetPasswordRequest $request)
    {
        try {

            // Reset password service
            $this->authService->resetPassword($request);

            return Response::success([
                'code' => Response::HTTP_OK,
                'message' => ResponseMessage::passwordResetSuccess(),
            ]);

        } catch (Exception $e) {

            return Response::fail([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Method for generating reset password link
    public function generateResetPasswordLink(ResetPasswordLinkRequest $request)
    {
        try {

            // Reset password link service
            $this->authService->resetPasswordLink($request);

            return Response::success([
                'code' => Response::HTTP_OK,
                'message' => ResponseMessage::passwordResetLink($request->email),
            ]);

        } catch (Exception $e) {

            return Response::fail([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Method for verifying user token
    public function verifyToken()
    {
        try {

            // Verify token service
            $userWithToken = $this->authService->verifyToken();

            return Response::success([
                'code' => Response::HTTP_OK,
                'user' => $userWithToken,
            ]);

        } catch (Exception $e) {

            return Response::fail([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }
}