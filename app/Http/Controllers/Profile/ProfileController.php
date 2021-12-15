<?php

namespace App\Http\Controllers\Profile;

use App\Helpers\Response;
use App\Helpers\ResponseMessage;
use App\Helpers\UpdateService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\UpdateProfileRequest;
use App\Models\Profile;

class ProfileController extends Controller
{
    // Update Profile
    public function update(UpdateProfileRequest $request)
    {
        // Allowed fields when updating a task
        $allowedFields = [
            'first_name',
            'last_name',
            'profile_image',
            'gender',
            'email_professional',
            'mobile_phone',
            'dob',
            'address_line_1',
            'address_line_2',
            'city',
            'county',
            'country',
            'zip_code',
        ];

        // Checking if the $request doesn't contain any of the allowed fields
        if (!$request->hasAny($allowedFields)) {
            return Response::fail([
                'message' => ResponseMessage::allowedFields($allowedFields),
                'code' => 400,
            ]);
        }

        // Get profile
        $profile = Profile::findOrFail($request->profile);

        // Update Profile
        $profileUpdated = UpdateService::updateModel($profile, $request->all(), 'profile');

        if (!$profileUpdated) {
            return Response::fail([
                'code' => 400,
                'message' => ResponseMessage::customMessage('Something went wrong. Cannot update profile at this time'),
            ]);
        }

        // Return updated profile
        return Response::success([
            'profile' => $profile->latest('updated_at')->first(),
        ]);
    }
}