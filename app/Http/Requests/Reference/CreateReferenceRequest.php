<?php

namespace App\Http\Requests\Reference;

use App\Helpers\CustomValidationService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class CreateReferenceRequest extends FormRequest
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
            'user' => 'required|numeric|exists:users,id',
            'reference_type' => 'required|string',
            'referee_name' => 'required|string',
            'company_name' => 'required|string',
            'relationship' => 'required|string',
            'referee_job_title' => 'required|string',
            'phone_number' => 'required|string',
            'referee_email' => 'required|email',
            'start_date' => 'required|date|date_format:Y-m-d',
            'end_date' => 'required|date|date_format:Y-m-d',
            'can_contact_referee' => 'required|boolean',
            'reference_document' => 'required|file|mimes:png,jpg,pdf,doc,docx|max:5000',
        ];
    }

    public function failedValidation($validator)
    {
        throw new ValidationException($validator, CustomValidationService::error_messages($this->rules(), $validator));
    }
}