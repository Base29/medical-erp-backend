<?php

namespace App\Http\Controllers\Profile;

use App\Helpers\Response;
use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\UpdateProfileRequest;
use App\Services\Profile\ProfileService;
use Exception;

class ProfileController extends Controller
{
    // Local variable
    protected $profileService;

    // Constructor
    public function __construct(ProfileService $profileService)
    {
        // Inject service
        $this->profileService = $profileService;
    }

    // Update Profile
    public function update(UpdateProfileRequest $request)
    {
        try {
            // update profile
            return $this->profileService->updateProfile($request);

        } catch (Exception $e) {
            return Response::fail([
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
        }
    }
}