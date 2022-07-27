<?php
namespace App\Services\User;

use App\Helpers\Response;
use App\Helpers\ResponseMessage;
use App\Helpers\UpdateService;
use App\Models\Applicant;
use App\Models\ContractSummary;
use App\Models\Department;
use App\Models\HiringRequest;
use App\Models\MiscellaneousInformation;
use App\Models\PositionSummary;
use App\Models\Profile;
use App\Models\User;
use App\Notifications\WelcomeNewEmployeeNotification;
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
        $user->generic_user = $request->generic_user;
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

        // Create misc info
        $miscInfo = new MiscellaneousInformation();
        $miscInfo->job_specification = null;
        $user->miscInfo()->save($miscInfo);

        // // Create education
        // $education = new Education();
        // $education->institution = null;
        // $user->education()->save($education);

        // // Create employment history
        // $employmentHistory = new EmploymentHistory();
        // $employmentHistory->employer_name = null;
        // $user->employmentHistories()->save($employmentHistory);

        // // Create reference
        // $reference = new Reference();
        // $reference->reference_type = null;
        // $user->references()->save($reference);

        // // Create legal
        // $legal = new Legal();
        // $legal->name = null;
        // $user->legal()->save($legal);

        // Add user as a applicant to the hiring request
        if ($hiringRequest) {
            // Instance of Applicant
            $applicant = new Applicant();
            $applicant->hiring_request_id = $hiringRequest->id;
            $applicant->user_id = $user->id;

            // Save applicant
            $applicant->save();
        }

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
    public function fetchUsers($request)
    {

        // Check if $request->filter exists
        if ($request->has('filter')) {

            // Allowed search filters
            $allowedFilters = [
                'mobile_phone',
                'last_name',
                'email',
                'role',
                'is_active',
                'is_candidate',
                'is_hired',
                'is_locum',
            ];

            // Check if $request->filter === $allowedFilters
            $filterIsAllowed = in_array($request->filter, $allowedFilters);

            if (!$filterIsAllowed) {
                throw new \Exception(ResponseMessage::allowedFilters($allowedFilters));
            }

            if ($request->filter === 'mobile_phone' || $request->filter === 'last_name') {
                // Filter users by mobile_phone or last_name
                $users = User::with('profile', 'positionSummary', 'contractSummary', 'roles', 'practices', 'employmentCheck', 'workPatterns.workTimings')
                    ->whereHas('profile', function ($q) {
                        $q->where(request()->filter, request()->value);
                    })
                    ->latest()
                    ->paginate(10);
            } elseif ($request->filter === 'email' || $request->filter === 'is_active' || $request->filter === 'is_candidate' || $request->filter === 'is_hired' || $request->filter === 'is_locum') {
                // Filter users by email
                $users = User::where($request->filter, $request->value)->with('profile', 'positionSummary', 'contractSummary', 'roles', 'practices', 'employmentCheck', 'workPatterns.workTimings')
                    ->latest()
                    ->paginate(10);

            } elseif ($request->filter === 'role') {
                // Filter users by role
                $users = User::with('profile', 'positionSummary', 'contractSummary', 'roles', 'practices', 'employmentCheck', 'workPatterns.workTimings')
                    ->whereHas('roles', function ($q) {
                        $q->where('id', request()->value);
                    })
                    ->latest()
                    ->paginate(10);
            }

        } else {
            // Fetching all the users from database
            $users = User::with('profile', 'positionSummary', 'contractSummary', 'roles', 'practices', 'employmentCheck', 'workPatterns.workTimings')
                ->latest()
                ->paginate(10);
        }

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

        UpdateService::updateModel($profile, $request->validated(), 'user');

        return Response::success([
            'user' => $profile::with('user', 'user.positionSummary', 'user.contractSummary', 'user.roles', 'user.practices', 'user.workPatterns.workTimings')
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
            ->with('profile.hiringRequest', 'positionSummary', 'contractSummary', 'roles', 'practices', 'employmentCheck', 'workPatterns.workTimings')
            ->firstOrFail();

        // Return details of the user
        return Response::success([
            'user' => $user,
        ]);
    }

    // Fetch single user
    public function fetchSingleUser($request)
    {
        // Get user from database
        $user = User::where('id', $request->user)
            ->with('profile.applicant', 'positionSummary', 'contractSummary', 'roles', 'practices', 'employmentCheck', 'workPatterns.workTimings')
            ->firstOrFail();

        // Return details of the user
        return Response::success([
            'user' => $user,
        ]);
    }

    // Generate password for candidate
    public function hireCandidate($request)
    {
        // Get user
        $candidate = User::where('id', $request->candidate)
            ->with('profile')
            ->firstOrFail();

        // Check if candidate is hired
        if (!$candidate->is_candidate) {
            throw new \Exception(ResponseMessage::customMessage('User ' . $candidate->id . ' is not a candidate'));
        }

        // Check if $candidate is already hired
        if ($candidate->is_hired) {
            throw new \Exception(ResponseMessage::customMessage('User ' . $candidate->id . ' is already hired'));
        }

        // Fetch hiring request
        $hiringRequest = HiringRequest::where('id', $candidate->profile->hiring_request_id)->firstOrFail();

        // Generate password
        $password = Str::random(16);

        // Save user pass and make user active
        $candidate->password = Hash::make($password);
        $candidate->is_hired = 1;
        $candidate->is_active = 1;
        $candidate->save();

        $candidate->givePermissionTo('can_manage_own_profile');
        $candidate->workPatterns()->attach($hiringRequest->workPatterns[0]->id);

        $credentials = [
            'email' => $candidate->email,
            'password' => $password,
        ];

        $candidate->notify(new WelcomeNewEmployeeNotification($credentials));

        // Return success response
        return Response::success([
            'candidate' => $candidate,
        ]);
    }
}