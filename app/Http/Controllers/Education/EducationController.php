<?php

namespace App\Http\Controllers\Education;

use App\Helpers\FileUploadService;
use App\Helpers\Response;
use App\Http\Controllers\Controller;
use App\Http\Requests\Education\CreateEducationRequest;
use App\Http\Requests\Education\FetchEducationRequest;
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
}