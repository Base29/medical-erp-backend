<?php

namespace App\Http\Requests\Reference;

use App\Helpers\CustomValidationService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class UpdateReferenceRequest extends FormRequest
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
            'reference' => 'required|numeric|exists:references,id',
            'reference_type' => 'nullable|string',
            'referee_name' => 'nullable|string',
            'company_name' => 'nullable|string',
            'relationship' => 'nullable|string',
            'referee_job_title' => 'nullable|string',
            'phone_number' => 'nullable|string',
            'referee_email' => 'nullable|email',
            'start_date' => 'nullable|date|date_format:Y-m-d',
            'end_date' => 'nullable|date|date_format:Y-m-d',
            'can_contact_referee' => 'nullable|boolean',
            'reference_document' => 'nullable|file|mimes:png,jpg,pdf,doc,docx|max:5000',
        ];
    }
    public function failedValidation($validator)
    {
        throw new ValidationException($validator, CustomValidationService::error_messages($this->rules(), $validator));
    }
}