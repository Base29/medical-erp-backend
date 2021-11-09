<?php

namespace App\Http\Controllers\User;

use App\Helpers\FileUploadService;
use App\Helpers\Response;
use App\Helpers\ResponseMessage;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\CreateUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use UpdateService;

class UserController extends Controller
{
    // Method for creating user
    public function create(CreateUserRequest $request)
    {
        try {

            // Initiating a null variable for profile image
            $profileImage = null;

            // Check if the profile_image is present and filled
            if ($request->has('profile_image') || $request->filled('profile_image')) {
                // Upload user profile picture
                $url = FileUploadService::upload($request->file('profile_image'), 'profileImages', 's3');

                // Assigning value of $url to $profileImage
                $profileImage = $url;
            }

            // Create user
            $user = new User();
            $user->email = $request->email;
            $user->first_name = $request->first_name;
            $user->middle_name = $request->middle_name;
            $user->maiden_name = $request->maiden_name;
            $user->last_name = $request->last_name;
            $user->password = Hash::make($request->password);
            $user->profile_image = $profileImage ? $profileImage : null;
            $user->gender = $request->gender;
            $user->email_professional = $request->email_professional;
            $user->work_phone = $request->work_phone;
            $user->home_phone = $request->home_phone;
            $user->mobile_phone = $request->mobile_phone;
            $user->dob = $request->dob;
            $user->address = $request->address;
            $user->city = $request->city;
            $user->county = $request->county;
            $user->country = $request->country;
            $user->zip_code = $request->zip_code;
            $user->nhs_card = $request->nhs_card;
            $user->nhs_number = $request->nhs_number;
            $user->save();

            return Response::success(['user' => $user]);

        } catch (\Exception $e) {

            return Response::fail([
                'code' => 500,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Method for deleting user
    public function delete($id)
    {
        try {

            // Check if the user exists with the provided $id
            $user = User::findOrFail($id);

            if (!$user) {
                return Response::fail([
                    'message' => ResponseMessage::notFound('User', $id, false),
                    'code' => 404,
                ]);
            }

            // Delete user with the provided $id
            $user->delete();

            return Response::success(['message' => ResponseMessage::deleteSuccess('User')]);

        } catch (\Exception $e) {

            return Response::fail([
                'code' => 500,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Method for fetching users
    public function fetch()
    {
        try {

            // Fetching all the users from database
            $users = User::with('roles', 'practices')->latest()->paginate(10);

            return Response::success(['users' => $users]);

        } catch (\Exception $e) {

            return Response::fail([
                'code' => 500,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Method for updating user
    public function update(UpdateUserRequest $request)
    {
        try {

            // Allowed fields when updating a task
            $allowedFields = [
                'first_name',
                'last_name',
                'profile_image',
                'gender',
                'email_professional',
                'mobile_phone',
                'dob',
                'address',
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

            // Fetch User
            $user = User::findOrFail($request->user);

            $userUpdated = UpdateService::updateModel($user, $request->all(), 'user');

            if ($userUpdated) {
                return Response::success([
                    'user' => $user->latest('updated_at')->first(),
                ]);
            }

        } catch (\Exception $e) {

            return Response::fail([
                'code' => 500,
                'message' => $e->getMessage(),
            ]);
        }
    }
}