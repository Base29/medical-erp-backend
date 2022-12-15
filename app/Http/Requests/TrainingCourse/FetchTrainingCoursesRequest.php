<?php

namespace App\Http\Requests\TrainingCourse;

use App\Helpers\CustomValidationService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class FetchTrainingCoursesRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'first_name' => 'nullable|string|max:50',
            'last_name' => 'nullable|string|max:50',
            'start_date' => 'nullable|date|date_format:Y-m-d',
            'due_date' => 'nullable|date|date_format:Y-m-d',
            'job_role' => 'nullable|numeric|exists:roles,id',
            'status' => [
                'nullable',
                'string',
                Rule::in([
                    'completed',
                    'in-progress',
                    'overdue',
                ]),
            ],
        ];
    }

    public function failedValidation($validator)
    {
        throw new ValidationException($validator, CustomValidationService::error_messages($this->rules(), $validator));
    }
}