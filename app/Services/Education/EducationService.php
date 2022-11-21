<?php
namespace App\Services\Education;

use App\Helpers\FileUploadService;
use App\Helpers\Response;
use App\Helpers\ResponseMessage;
use App\Helpers\UpdateService;
use App\Models\Education;
use App\Models\User;
use Exception;

class EducationService
{
    // Create education
    public function createEducation($request)
    {

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

        // Return Education
        return $education;
    }

    // Fetch user education
    public function fetchEducation($request)
    {
        // Get user
        $user = User::findOrFail($request->user);

        // Get Education information
        return Education::where('user_id', $user->id)->latest()->paginate(10);
    }

    // Delete education
    public function deleteEducation($request)
    {
        // Get education
        $education = Education::findOrFail($request->education);

        // Delete Education
        $education->delete();
    }

    // Update education
    public function updateEducation($request)
    {
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
            throw new Exception(ResponseMessage::allowedFields($allowedFields), Response::HTTP_BAD_REQUEST);
        }

        // Get education
        $education = Education::findOrFail($request->education);

        $updateRequestData = $request->validated();

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
            throw new Exception(ResponseMessage::customMessage('Something went wrong. Cannot update Education at this moment'), Response::HTTP_BAD_REQUEST);
        }

        // Return success response

        return $education->latest('updated_at')->first();

    }
}