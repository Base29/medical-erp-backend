<?php
namespace App\Services\User;

use App\Helpers\Response;
use App\Helpers\ResponseMessage;
use App\Helpers\UpdateService;
use App\Models\ContractSummary;
use App\Models\Department;
use App\Models\HiringRequest;
use App\Models\PositionSummary;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserService
{
    // Create user
    public function createUser($request)
    {
        // Check if the user being created is a candidate
        if ($request->is_candidate) {
            $requiredFields = [
                'gender',
                'mobile_phone',
                'job_title',
                'contract_type',
                'contract_start_date',
                'contracted_hours_per_week',
                'hiring_request',
                'department',
            ];

            if (!$request->hasAny($requiredFields)) {
                throw new \Exception(ResponseMessage::customMessage('Candidate must have all the required fields ' . implode(' | ', $requiredFields)));
            }

            // Get hiring request
            $hiringRequest = HiringRequest::findOrFail($request->hiring_request);

            // Get Department
            $department = Department::findOrFail($request->department);
        }

        // Check if the user is not a candidate so the password field is required
        if (!$request->is_candidate && (!$request->has('password') || !$request->has('password_confirmation'))) {
            throw new \Exception(ResponseMessage::customMessage('The password and password_confirmation fields are required'));
        }

        // // Initiating a null variable for profile image
        // $profileImage = null;

        // // Check if the profile_image is present and filled
        // if ($request->has('profile_image') || $request->filled('profile_image')) {
        //     // Upload user profile picture
        //     $url = FileUploadService::upload($request->file('profile_image'), 'profileImages', 's3');

        //     // Assigning value of $url to $profileImage
        //     $profileImage = $url;
        // }

        // Generating a random password for the candidate
        $random = Str::random(40);

        // Create user
        $user = new User();
        $user->email = $request->email;
        $user->password = Hash::make($request->is_candidate ? $random : $request->password);
        $user->is_active = $request->is_candidate ? 0 : 1;
        $user->is_candidate = $request->is_candidate ? $request->is_candidate : 0;
        $user->department_id = $request->is_candidate ? $department->id : null;
        $user->save();

        // Create profile for the user
        $profile = new Profile();
        $profile->first_name = $request->first_name;
        $profile->last_name = $request->last_name;
        $profile->gender = $request->is_candidate ? $request->gender : null;
        $profile->mobile_phone = $request->is_candidate ? $request->mobile_phone : null;
        $profile->primary_role = $request->is_candidate ? $request->job_title : null;
        $profile->hiring_request_id = $request->is_candidate ? $hiringRequest->id : null;
        $user->profile()->save($profile);

        // Create position summary
        $positionSummary = new PositionSummary();
        $positionSummary->job_title = $request->is_candidate ? $request->job_title : null;
        $positionSummary->contract_type = $request->is_candidate ? $request->contract_type : null;
        $user->positionSummary()->save($positionSummary);

        // Parsing date format to Y-m-d
        $formattedDate = Carbon::parse($request->contract_start_date)
            ->format('Y-m-d');

        // Create contract summary
        $contractSummary = new ContractSummary();
        $contractSummary->contract_start_date = $request->is_candidate ? $request->contract_start_date : null;
        $contractSummary->contracted_hours_per_week = $request->is_candidate ? $request->contracted_hours_per_week : null;
        $user->contractSummary()->save($contractSummary);

        // Assigning role(s) if user being created is a candidate
        if ($request->is_candidate) {
            // Assigning primary role (position) to user
            $user->assignRole($request->job_title);

            // Check if request has additional_roles array
            if ($request->has('additional_roles')) {
                // Assigning additional roles to user
                foreach ($request->additional_roles as $additional_role) {
                    $user->assignRole($additional_role);
                }
            }
        }

        return Response::success([
            'user' => $user
                ->with('profile.hiringRequest', 'positionSummary', 'contractSummary', 'roles', 'practices')
                ->latest()
                ->first(),
        ]);
    }

    // Delete user
    public function deleteUser($id)
    {
        // Check if the user exists with the provided $id
        $user = User::findOrFail($id);

        if (!$user) {
            throw new \Exception(ResponseMessage::notFound('User', $id, false));
        }

        // Delete user with the provided $id
        $user->delete();

        return Response::success(['message' => ResponseMessage::deleteSuccess('User')]);
    }

    // Fetch users
    public function fetchUsers()
    {
        // Fetching all the users from database
        $users = User::with('profile', 'positionSummary', 'contractSummary', 'roles', 'practices', 'employmentCheck')
            ->latest()
            ->paginate(10);

        return Response::success(['users' => $users]);
    }

    // Update user
    public function updateUser($request)
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

        // Fetch User
        $user = User::findOrFail($request->user);

        // Get profile for the user
        $profile = Profile::where('user_id', $user->id)->firstOrFail();

        UpdateService::updateModel($profile, $request->all(), 'user');

        return Response::success([
            'user' => $profile::with('user', 'user.positionSummary', 'user.contractSummary', 'user.roles', 'user.practices')
                ->latest('updated_at')
                ->first(),
        ]);

    }

    // Me
    public function me()
    {
        // Get ID of the logged in user
        $authenticatedUser = auth()->user()->id;

        // Get user from database
        $user = User::where('id', $authenticatedUser)
            ->with('profile.hiringRequest', 'positionSummary', 'contractSummary', 'roles', 'practices', 'employmentCheck')
            ->get();

        // Return details of the user
        return Response::success([
            'user' => $user,
        ]);
    }
}