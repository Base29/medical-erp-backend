<?php

/**
 *
 */

namespace App\Services\HiringRequest;

use App\Helpers\Response;
use App\Helpers\ResponseMessage;
use App\Helpers\UpdateService;
use App\Models\Applicant;
use App\Models\Department;
use App\Models\HiringRequest;
use App\Models\HiringRequestPosting;
use App\Models\JobSpecification;
use App\Models\PersonSpecification;
use App\Models\Practice;
use App\Models\Role;
use App\Models\User;
use App\Models\WorkPattern;
use App\Models\WorkTiming;
use Exception;
use Illuminate\Support\Facades\Config;

class HiringRequestService
{
    // Hiring Request Create
    public function createHiringRequest($request)
    {

        // Get practice
        $practice = Practice::findOrFail($request->practice);

        // Get job specification
        $jobSpecification = JobSpecification::findOrFail($request->job_specification);

        // Get person specification
        $personSpecification = PersonSpecification::findOrFail($request->person_specification);

        // Get department
        $department = Department::findOrFail($request->department);

        // Get role
        $role = Role::where('id', $request->role)->firstOrFail();

        // Get role
        $reportingTo = User::where('id', $request->reporting_to)->with('profile')->firstOrFail();

        // Get work pattern
        $workPattern = WorkPattern::find($request->rota_information);

        // Cast id of $workPattern to a variable
        $workPatternId = $workPattern ? $workPattern->id : null;

        // Get current authenticated user
        $authenticatedUser = auth()->user();

        // If $workPattern is false
        if (!$workPattern) {

            // Fields required for creating a new work pattern
            $requiredFields = [
                'name',
                'start_time',
                'end_time',
                'break_time',
                'repeat_days',
            ];

            // Checking if the $request doesn't contain any of the allowed fields
            if (!$request->has($requiredFields)) {
                throw new Exception(ResponseMessage::customMessage('Selected Rota with id ' . $request->rota_information . ' is invalid. Supply following fields to create new rota ' . implode("|", $requiredFields)), Response::HTTP_BAD_REQUEST);
            }

            // Create work pattern
            $workPattern = new WorkPattern();
            $workPattern->name = $request->name;
            $workPattern->save();

            // Create Work Timing
            $workTiming = new WorkTiming();
            $workTiming->work_pattern_id = $workPattern->id;
            $workTiming->start_time = $request->start_time;
            $workTiming->end_time = $request->end_time;
            $workTiming->break_time = $request->break_time;
            $workTiming->repeat_days = $request->repeat_days;
            $workPattern->workTimings()->save($workTiming);

            $workPatternId = $workPattern->id;

        }

        // Instance of HiringRequest
        $hiringRequest = new HiringRequest();
        $hiringRequest->notifiable = $authenticatedUser->id;
        $hiringRequest->job_title = $request->job_title;
        $hiringRequest->role = $role->id;
        $hiringRequest->contract_type = $request->contract_type;
        $hiringRequest->department_id = $department->id;
        $hiringRequest->reporting_to = $reportingTo->profile->first_name . ' ' . $reportingTo->profile->last_name;
        $hiringRequest->start_date = $request->start_date;
        $hiringRequest->starting_salary = $request->starting_salary;
        $hiringRequest->reason_for_recruitment = $request->reason_for_recruitment;
        $hiringRequest->comment = $request->comment;
        $hiringRequest->status = Config::get('constants.HIRING_REQUEST.PENDING');
        $hiringRequest->job_specification_id = $jobSpecification->id;
        $hiringRequest->person_specification_id = $personSpecification->id;
        $hiringRequest->application_manager = auth()->user()->id;
        // Save hiring request
        $practice->hiringRequests()->save($hiringRequest);

        // Attach work pattern with the hiring request
        $hiringRequest->workPatterns()->attach($workPatternId);

        // Return newly created $hiringRequest
        return $hiringRequest->with(['applicationManager.profile', 'practice', 'workPatterns.workTimings', 'jobSpecification.responsibilities', 'personSpecification.personSpecificationAttributes', 'profiles', 'department', 'applicants.profile', 'applicants.offers.amendments' => function ($q) {
            $q->orderBy('id', 'desc')->limit(1);
        }, 'hiringRequestPostings'])
            ->withCount('applicants')
            ->latest()
            ->first();
    }

    // Fetch single hiring request
    public function fetchSingleHiringRequest($request)
    {
        // Get hiring request
        return HiringRequest::where('id', $request->hiring_request)
            ->with(['applicationManager.profile', 'practice', 'workPatterns.workTimings', 'jobSpecification.responsibilities', 'personSpecification.personSpecificationAttributes', 'profiles', 'department', 'applicants.profile', 'applicants.offers' => function ($q) {
                $q->where('is_active', 1);
            }, 'applicants.offers.amendments', 'hiringRequestPostings'])
            ->get();
    }

    // Update hiring request
    public function updateHiringRequest($request)
    {
        // Allowed fields
        $allowedFields = [
            'job_title',
            'contract_type',
            'reporting_to',
            'start_date',
            'starting_salary',
            'reason_for_recruitment',
            'comment',
            'is_live',
            'job_specification_id',
            'person_specification_id',
            'department_id',
        ];

        // Checking if the $request doesn't contain any of the allowed fields
        if (!$request->hasAny($allowedFields)) {
            throw new Exception(ResponseMessage::allowedFields($allowedFields), Response::HTTP_BAD_REQUEST);
        }

        // Get hiring request
        $hiringRequest = HiringRequest::findOrFail($request->hiring_request);

        // Cast $request->validated() to variable
        $updateRequestData = $request->validated();

        // Check if $request->reporting_to exists
        if ($request->has('reporting_to')) {
            // Get Reporting to user
            $reportingTo = User::where('id', $request->reporting_to)->with('profile')->firstOrFail();

            $updateRequestData['reporting_to'] = $reportingTo->profile->first_name . ' ' . $reportingTo->profile->last_name;
        }

        // Check if $request->job_specification exists
        if ($request->has('job_specification_id')) {
            // Get job specification
            $jobSpecification = JobSpecification::findOrFail($request->job_specification_id);

            $updateRequestData['job_specification_id'] = $jobSpecification->id;
        }

        // Check if $request->person_specification exists
        if ($request->has('person_specification_id')) {
            // Get person specification
            $personSpecification = PersonSpecification::findOrFail($request->person_specification_id);

            $updateRequestData['person_specification_id'] = $personSpecification->id;
        }

        // Check if $request->department exists
        if ($request->has('department_id')) {
            // Get department
            $department = Department::findOrFail($request->department_id);

            $updateRequestData['department_id'] = $department->id;
        }

        // // If updating only work pattern
        // if ($request->has('rota_information')) {
        //     // Get work pattern
        //     $workPattern = WorkPattern::find($request->rota_information);

        //     // If work pattern doesn't exist with the provided $request->rota_information
        //     if (!$workPattern) {
        //         throw new Exception(ResponseMessage::customMessage('Work Pattern with the provided id ' . $request->rota_information . ' not found. Please provide correct work pattern id or create a new work pattern'));
        //     }

        //     // Cast id of $workPattern to a variable
        //     $workPatternId = $workPattern ? $workPattern->id : null;

        //     // If $workPattern is false
        //     if (!$workPattern) {

        //         // Fields required for creating a new work pattern
        //         $requiredFields = [
        //             'name',
        //             'start_time',
        //             'end_time',
        //             'break_time',
        //             'repeat_days',
        //         ];

        //         // Checking if the $request doesn't contain any of the allowed fields
        //         if (!$request->has($requiredFields)) {
        //             throw new Exception(ResponseMessage::customMessage('Selected Rota with id ' . $request->rota_information . ' is invalid. Supply following fields to create new rota ' . implode("|", $requiredFields)));
        //         }

        //         // Create work pattern
        //         $workPattern = new WorkPattern();
        //         $workPattern->name = $request->name;
        //         $workPattern->save();

        //         // Create Work Timing
        //         $workTiming = new WorkTiming();
        //         $workTiming->work_pattern_id = $workPattern->id;
        //         $workTiming->start_time = $request->start_time;
        //         $workTiming->end_time = $request->end_time;
        //         $workTiming->break_time = $request->break_time;
        //         $workTiming->repeat_days = $request->repeat_days;
        //         $workPattern->workTimings()->save($workTiming);

        //         $workPatternId = $workPattern->id;

        //     }

        //     // Attach work pattern with the hiring request
        //     $hiringRequest->workPatterns()->attach($workPatternId);

        //     // Return success response
        //     return $hiringRequest->where('id', $request->rota_information)
        //         ->with('applicationManager.profile', 'practice', 'workPatterns.workTimings', 'jobSpecification.responsibilities', 'personSpecification.personSpecificationAttributes', 'profiles', 'department', 'applicants.profile.user.offer', 'hiringRequestPostings')
        //         ->get();

        // }

        // Update hiring request except work pattern
        $hiringRequestUpdated = UpdateService::updateModel($hiringRequest, $updateRequestData, 'hiring_request');

        // Return fail response
        if (!$hiringRequestUpdated) {
            throw new Exception(ResponseMessage::customMessage('Something went wrong while updating Hiring Request'), Response::HTTP_BAD_REQUEST);
        }

        // Return success response
        return $hiringRequest->with(['applicationManager.profile', 'practice', 'workPatterns.workTimings', 'jobSpecification.responsibilities', 'personSpecification.personSpecificationAttributes', 'profiles', 'department', 'applicants.profile', 'applicants.offers.amendments' => function ($q) {
            $q->orderBy('id', 'desc')->limit(1);
        }, 'hiringRequestPostings'])
            ->latest('updated_at')
            ->first();
    }

    // Delete hiring request
    public function deleteHiringRequest($request)
    {
        // Get hiring request
        $hiringRequest = HiringRequest::findOrFail($request->hiring_request);

        // Delete hiring request
        $hiringRequest->delete();
    }

    // Fetch Hiring requests
    public function fetchHiringRequests($request)
    {

        // Initiate query for HiringRequests
        $hiringRequestQuery = HiringRequest::query();

        // Check if $request has status
        if ($request->has('status')) {
            $hiringRequestQuery = $hiringRequestQuery->where('status', $request->status);
        }

        // Check if $request has contract_type
        if ($request->has('contract_type')) {
            $hiringRequestQuery = $hiringRequestQuery->where('contract_type', $request->contract_type);
        }

        // Check if $request has role
        if ($request->has('role')) {
            $role = Role::findOrFail($request->role);

            $hiringRequestQuery = $hiringRequestQuery->where('role', $role->id);
        }

        // Check if $request has job_title
        if ($request->has('job_title')) {
            $hiringRequestQuery = $hiringRequestQuery->where('job_title', 'like', '%' . $request->job_title . '%');
        }

        // Check if $request has progress
        if ($request->has('progress')) {
            $hiringRequestQuery = $hiringRequestQuery->where('progress', $request->progress);
        }

        // Cast $hiringRequestQuery to $hiringRequests
        $hiringRequests = $hiringRequestQuery->with(['applicationManager.profile', 'practice', 'workPatterns.workTimings', 'jobSpecification.responsibilities', 'personSpecification.personSpecificationAttributes', 'profiles', 'department', 'applicants.profile', 'applicants.offers.amendments', 'hiringRequestPostings'])
            ->latest()
            ->paginate(10);

        // Casting $hiringRequests to $results and converting the object to array
        $results = $hiringRequests->toArray();

        /**
         * Count according to status
         */

        // Getting count of pending hiring requests
        $pending = $this->processCount('status', Config::get('constants.HIRING_REQUEST.PENDING'), $request);

        // Getting count of approved hiring requests
        $approved = $this->processCount('status', Config::get('constants.HIRING_REQUEST.APPROVED'), $request);

        // Getting count of declined hiring requests
        $declined = $this->processCount('status', Config::get('constants.HIRING_REQUEST.DECLINED'), $request);

        // Getting count of escalated hiring requests
        $escalated = $this->processCount('status', Config::get('constants.HIRING_REQUEST.ESCALATED'), $request);

        /**
         * Count according to contract type
         */

        // Getting count of permanent contract
        $permanent = $this->processCount('contract_type', Config::get('constants.HIRING_REQUEST.CONTRACT_TYPE.PERMANENT'), $request);

        // Getting count of fixed term contract
        $fixedTerm = $this->processCount('contract_type', Config::get('constants.HIRING_REQUEST.CONTRACT_TYPE.FIXED_TERM'), $request);

        // Getting count of casual contract
        $casual = $this->processCount('contract_type', Config::get('constants.HIRING_REQUEST.CONTRACT_TYPE.CASUAL'), $request);

        // Getting count of zero hour contract
        $zeroHour = $this->processCount('contract_type', Config::get('constants.HIRING_REQUEST.CONTRACT_TYPE.ZERO_HOUR'), $request);

        // Adding extra meta to response $results
        $results['count']['pending'] = $pending;
        $results['count']['approved'] = $approved;
        $results['count']['declined'] = $declined;
        $results['count']['escalated'] = $escalated;
        $results['count']['permanent'] = $permanent;
        $results['count']['fixed-term'] = $fixedTerm;
        $results['count']['casual'] = $casual;
        $results['count']['zero-hour'] = $zeroHour;

        // Return hiring requests
        return $results;
    }

    // Process count
    private function processCount($column, $value, $request)
    {
        return HiringRequest::where([$column => $value])->count();
    }

    // Add applicant to hiring request
    public function addApplicantToHiringRequest($request)
    {
        // Get hiring request
        $hiringRequest = HiringRequest::findOrFail($request->hiring_request);

        // Get user
        $user = User::findOrFail($request->user);

        // Instance of Applicant
        $applicant = new Applicant();
        $applicant->hiring_request_id = $hiringRequest->id;
        $applicant->user_id = $user->id;

        // Save applicant
        $applicant->save();

        // Return success response
        return Response::success([
            'code' => Response::HTTP_OK,
            'applicant' => $applicant->with('profile', 'vacancy')->latest()->first(),
        ]);
    }

    // Create hiring request posting
    public function createHiringRequestPosting($request)
    {
        // Get hiring request
        $hiringRequest = HiringRequest::findOrFail($request->hiring_request);

        // Instance of HiringRequestPosting
        $hiringRequestPosting = new HiringRequestPosting();
        $hiringRequestPosting->site_name = $request->site_name;
        $hiringRequestPosting->post_date = $request->post_date;
        $hiringRequestPosting->end_date = $request->end_date;
        $hiringRequestPosting->link = $request->link;

        // Save
        $hiringRequest->hiringRequestPostings()->save($hiringRequestPosting);

        // Return success response
        return Response::success([
            'code' => Response::HTTP_CREATED,
            'postings' => $hiringRequestPosting,
        ]);
    }

    // Fetch all applicants
    public function fetchAllApplicants($request)
    {
        // Get hiring request
        $hiringRequest = HiringRequest::findOrFail($request->hiring_request);

        // Applicants of $hiringRequest
        $applicants = Applicant::where('hiring_request_id', $hiringRequest->id)
            ->with('profile', 'offers')
            ->latest()
            ->paginate(10);

        // Return success response
        return Response::success([
            'code' => Response::HTTP_OK,
            'applicants' => $applicants,
        ]);

    }

    // Fetch postings
    public function fetchAllPostings($request)
    {
        // Get hiring request
        $hiringRequest = HiringRequest::findOrFail($request->hiring_request);

        // Get hiring request postings
        $postings = HiringRequestPosting::where('hiring_request_id', $hiringRequest->id)
            ->latest()
            ->get();

        // Return success response
        return Response::success([
            'code' => Response::HTTP_OK,
            'postings' => $postings,
        ]);

    }

    // Search Hiring Request
    public function searchVacancies($request)
    {
        // Get type of $request->value
        $valueType = gettype($request->value);

        /**
         * Filter with integer as a value
         * Add new filters which will be searching using ID to the below $filtersWithIntValue array
         */
        $filtersWithIntValue = [
            'role',
            'location',
            'manager',
            'department',
            'job_specification',
            'person_specification',
            'is_live',
        ];

        /**
         * Filter with string as a value
         * Add new filters which will be searching using string to the below $filtersWithStringValue array
         */
        $filtersWithStringValue = [
            'contract_type',
            'status',
            'progress',
        ];

        // Filter
        $filter = $request->filter;

        // Check if $filter is in $filtersWithIntValue array
        if (in_array($filter, $filtersWithIntValue)):

            // Check the type of $request->value is integer
            if ($valueType !== 'integer') {
                throw new Exception(ResponseMessage::customMessage('The value for the filter "' . $request->filter . '" should be of type integer'), Response::HTTP_BAD_REQUEST);
            }

            // Switch statement according to $filter
            switch ($filter) {

                case 'role':
                    // Check if role exists
                    $role = Role::findOrFail($request->value);

                    // Getting vacancies filtered by $role->id
                    $filteredVacancies = $this->filteredSearchResults('role', $role->id);

                    break;

                case 'location':
                    // Check if practice exists
                    $location = Practice::findOrFail($request->value);

                    // Getting vacancies filtered by $location->id
                    $filteredVacancies = $this->filteredSearchResults('practice_id', $location->id);

                    break;

                case 'manager':
                    // Check if the application manager exists
                    $applicationManager = User::findOrFail($request->value);

                    // Getting vacancies filtered by $applicationManager->id
                    $filteredVacancies = $this->filteredSearchResults('application_manager', $applicationManager->id);

                    break;

                case 'department':
                    // Check if the department exists
                    $department = Department::findOrFail($request->value);

                    // Getting vacancies filtered by department
                    $filteredVacancies = $this->filteredSearchResults('department_id', $department->id);

                    break;

                case 'job_specification':
                    // Check if job specification exists
                    $jobSpecification = JobSpecification::findOrFail($request->value);

                    // Getting vacancies filtered by job spcifications
                    $filteredVacancies = $this->filteredSearchResults('job_specification_id', $jobSpecification->id);

                    break;

                case 'person_specification':
                    // Check if person specification exists
                    $personSpecification = PersonSpecification::findOrFail($request->value);

                    // Getting vacancies filtered by person specification
                    $filteredVacancies = $this->filteredSearchResults('person_specification_id', $personSpecification->id);

                    break;

                case 'reporting_to':
                    // Check if reporting to user exists
                    $reportingTo = User::findOrFail($request->value);

                    // Get vacancies filtered by reporting to
                    $filteredVacancies = $this->filteredSearchResults('reporting_to', $reportingTo->id);

                    break;

                case 'is_live':
                    // Getting vacancies by is_live status
                    $filteredVacancies = $this->filteredSearchResults('is_live', $request->value);
                    break;

                default:
                    return false;
            }

        endif;

        // Check if $filter is in $filtersWithStringValue array

        if (in_array($filter, $filtersWithStringValue)):

            // Check the type of $valueType is string
            if ($valueType !== 'string') {
                throw new Exception(ResponseMessage::customMessage('The value for the filter "' . $request->filter . '" should be of type string'), Response::HTTP_BAD_REQUEST);
            }

            // Switch statement according to filter
            switch ($filter) {
                case 'contract_type':
                    // Getting vacancies filtered by contract type
                    $filteredVacancies = $this->filteredSearchResults('contract_type', $request->value);

                    break;

                case 'status':
                    // Getting vacancies filtered by status
                    $filteredVacancies = $this->filteredSearchResults('status', $request->value);

                    break;

                case 'progress':
                    // Getting vacancies filtered by progress
                    $filteredVacancies = $this->filteredSearchResults('progress', $request->value);

                    break;

                default:
                    return false;
            }

        endif;

        // Return success response
        return Response::success([
            'code' => Response::HTTP_OK,
            'filtered-vacancies' => $filteredVacancies,
        ]);

    }

    private function filteredSearchResults($filter, $value)
    {
        return HiringRequest::where($filter, $value)
            ->with('applicants.profile')
            ->latest()
            ->paginate(10);
    }
}