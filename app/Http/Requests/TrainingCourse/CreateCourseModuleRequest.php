<?php

namespace App\Http\Requests\TrainingCourse;

use App\Helpers\CustomValidationService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class CreateCourseModuleRequest extends FormRequest
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
            'course' => 'required|numeric|exists:training_courses,id',
            'name' => 'required|string',
            'duration' => 'required|string',
            'is_required' => 'required|boolean',
            'frequency' => 'nullable|string',
            'reminder' => 'nullable|numeric',
        ];
    }

    public function failedValidation($validator)
    {
        throw new ValidationException($validator, CustomValidationService::error_messages($this->rules(), $validator));
    }
}