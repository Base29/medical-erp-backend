<?php

namespace App\Http\Controllers\User;

use App\Helpers\Response;
use App\Helpers\ResponseMessage;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\CreateUserRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // Method for creating user
    public function create(CreateUserRequest $request)
    {
        try {

            // Create user
            $user = new User();
            $user->email = $request->email;
            $user->first_name = $request->first_name;
            $user->middle_name = $request->middle_name;
            $user->maiden_name = $request->maiden_name;
            $user->last_name = $request->last_name;
            $user->password = Hash::make($request->password);
            $user->profile_image = $request->profile_image;
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
}