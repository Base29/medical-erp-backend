<?php

namespace App\Http\Requests\TrainingCourse;

use App\Helpers\CustomValidationService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class CreateTrainingCourseRequest extends FormRequest
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
            'name' => 'required|string',
            'decription' => 'nullable|string|max:2000',
            'type' => [
                'required',
                Rule::in(['digital', 'physical']),
            ],
            'frequency' => 'nullable|string',
            'roles' => 'required|array',
            'roles.*.role' => 'required|numeric|exists:roles,id',
        ];
    }

    public function failedValidation($validator)
    {
        throw new ValidationException($validator, CustomValidationService::error_messages($this->rules(), $validator));
    }
}