<?php
namespace App\Services\Profile;

use App\Helpers\Response;
use App\Helpers\ResponseMessage;
use App\Helpers\UpdateService;
use App\Models\Profile;

class ProfileService
{
    // Update profile
    public function updateProfile($request)
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
            throw new \Exception(ResponseMessage::allowedFields($allowedFields));
        }

        // Get profile
        $profile = Profile::findOrFail($request->profile);

        // Update Profile
        $profileUpdated = UpdateService::updateModel($profile, $request->validated(), 'profile');

        if (!$profileUpdated) {
            throw new \Exception(ResponseMessage::customMessage('Something went wrong. Cannot update profile at this time'));
        }

        // Return updated profile
        return Response::success([
            'profile' => $profile->latest('updated_at')->first(),
        ]);
    }
}
