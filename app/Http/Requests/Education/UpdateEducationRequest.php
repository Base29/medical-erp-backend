<?php

namespace App\Http\Requests\Education;

use App\Helpers\CustomValidationService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class UpdateEducationRequest extends FormRequest
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
            'education' => 'required|numeric|exists:education,id',
            'institution',
            'subject' => 'nullable|string',
            'start_date' => 'nullable|date|date_format:Y-m-d',
            'completion_date' => 'nullable|date|date_format:Y-m-d',
            'degree' => 'nullable|string',
            'grade' => 'nullable|string',
            'certificate' => 'nullable|file|mimes:png,jpg,docx,doc,pdf',
        ];
    }

    public function failedValidation($validator)
    {
        throw new ValidationException($validator, CustomValidationService::error_messages($this->rules(), $validator));
    }
}