<?php

namespace App\Http\Requests\JobSpecification;

use App\Helpers\CustomValidationService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class CreateJobSpecificationRequest extends FormRequest
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
            'practice' => 'required|numeric|exists:practices,id',
            'title' => 'required|string|max:100',
            'salary_grade' => 'nullable|string|max:100',
            'location' => 'nullable|string|max:100',
            'total_hours' => 'nullable|string|max:50',
            'job_purpose' => 'nullable|string|max:1000',
            'responsibilities' => 'required|array',
            'responsibilities.*.responsibility' => 'required|string|max:500',
        ];
    }

    public function failedValidation($validator)
    {
        throw new ValidationException($validator, CustomValidationService::error_messages($this->rules(), $validator));
    }
}