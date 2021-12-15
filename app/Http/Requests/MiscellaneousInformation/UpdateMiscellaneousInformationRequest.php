<?php

namespace App\Http\Requests\MiscellaneousInformation;

use App\Helpers\CustomValidationService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class UpdateMiscellaneousInformationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'misc_info' => 'required|numeric|exists:miscellaneous_information,id',
            'job_description' => 'nullable|numeric|exists:job_specifications,id',
            'interview_notes' => 'nullable|string|min:0|max:1000',
            'offer_letter_email' => 'nullable|file|mimes:doc,docx,pdf,xls,xlsx',
            'job_advertisement' => 'nullable|file|mimes:doc,docx,pdf,xls,xlsx',
            'health_questionnaire' => 'nullable|file|mimes:doc,docx,pdf,xls,xlsx',
            'annual_declaration' => 'nullable|file|mimes:doc,docx,pdf,xls,xlsx',
            'employee_confidentiality_agreement' => 'nullable|file|mimes:doc,docx,pdf,xls,xlsx',
            'employee_privacy_notice' => 'nullable|file|mimes:doc,docx,pdf,xls,xlsx',
            'locker_key_agreement' => 'nullable|file|mimes:doc,docx,pdf,xls,xlsx',
            'is_locker_key_assigned' => 'boolean',
            'equipment_provided_policy' => 'nullable|file|mimes:doc,docx,pdf,xls,xlsx',
            'resume' => 'nullable|file|mimes:doc,docx,pdf,xls,xlsx',
            'proof_of_address' => 'nullable|file|mimes:doc,docx,pdf,xls,xlsx',
            'equipment' => 'array',
        ];
    }

    public function failedValidation($validator)
    {
        throw new ValidationException($validator, CustomValidationService::error_messages($this->rules(), $validator));
    }
}