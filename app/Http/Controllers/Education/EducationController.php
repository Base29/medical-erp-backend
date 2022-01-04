<?php

namespace App\Http\Controllers\Education;

use App\Helpers\FileUploadService;
use App\Helpers\Response;
use App\Helpers\ResponseMessage;
use App\Helpers\UpdateService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Education\CreateEducationRequest;
use App\Http\Requests\Education\DeleteEducationRequest;
use App\Http\Requests\Education\FetchEducationRequest;
use App\Http\Requests\Education\UpdateEducationRequest;
use App\Models\Education;
use App\Models\User;

class EducationController extends Controller
{
    // Create Education
    public function create(CreateEducationRequest $request)
    {
        try {
            // Get user
            $user = User::findOrFail($request->user);

            // Initiate a null variable
            $educationCertificateUrl = null;

            // Check if request has certificate file to upload
            if ($request->hasFile('certificate')) {
                // Folder path for education certificate
                $folderPath = 'education/user-' . $user->id;

                // Education certificate upload
                $educationCertificateUrl = FileUploadService::upload($request->certificate, $folderPath, 's3');
            }

            // Initiate a instance of Education model
            $education = new Education();
            $education->institution = $request->institution;
            $education->subject = $request->subject;
            $education->start_date = $request->start_date;
            $education->completion_date = $request->completion_date;
            $education->degree = $request->degree;
            $education->grade = $request->grade;
            $education->certificate = $educationCertificateUrl;

            // Save Education
            $user->education()->save($education);

            // Return success response
            return Response::success([
                'education' => $education,
            ]);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Fetch user's education
    public function fetch(FetchEducationRequest $request)
    {
        try {
            // Get user
            $user = User::findOrFail($request->user);

            // Get Education information
            $education = Education::where('user_id', $user->id)->paginate(10);

            // Return success response
            return Response::success([
                'education' => $education,
            ]);

        } catch (\Exception $e) {

            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Delete education
    public function delete(DeleteEducationRequest $request)
    {
        try {

            // Get education
            $education = Education::findOrFail($request->education);

            // Delete Education
            $education->delete();

            // Return success response
            return Response::success([
                'message' => ResponseMessage::deleteSuccess('Education'),
            ]);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }

    // Update education
    public function update(UpdateEducationRequest $request)
    {
        try {

            // Allowed fields
            $allowedFields = [
                'institution',
                'subject',
                'start_date',
                'completion_date',
                'degree',
                'grade',
                'certificate',
            ];

            // Checking if the $request doesn't contain any of the allowed fields
            if (!$request->hasAny($allowedFields)) {
                return Response::fail([
                    'message' => ResponseMessage::allowedFields($allowedFields),
                    'code' => 400,
                ]);
            }

            // Get education
            $education = Education::findOrFail($request->education);

            $updateRequestData = $request->all();

            // Check if request has file $request->certificate
            if ($request->hasFile('certificate')) {
                // Folder path for education certificate
                $folderPath = 'education/user-' . $education->user_id;

                // Upload file
                $updateRequestData['certificate'] = FileUploadService::upload($request->file('certificate'), $folderPath, 's3');

            }

            // Update education
            $educationUpdated = UpdateService::updateModel($education, $updateRequestData, 'education');

            if (!$educationUpdated) {
                return Response::fail([
                    'code' => 400,
                    'message' => ResponseMessage::customMessage('Something went wrong. Cannot update Education at this moment'),
                ]);
            }

            // Return success response
            return Response::success([
                'education' => $education->latest('updated_at')->first(),
            ]);

        } catch (\Exception $e) {
            return Response::fail([
                'code' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }
}