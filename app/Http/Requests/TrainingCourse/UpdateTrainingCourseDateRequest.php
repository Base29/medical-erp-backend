<?php

namespace App\Http\Requests\TrainingCourse;

use App\Helpers\CustomValidationService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class UpdateTrainingCourseDateRequest extends FormRequest
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
            'user' => 'required|numeric|exists:users,id',
            'course' => 'required|numeric|exists:training_courses,id',
            'dates' => 'required|array',
            'dates.start_date' => 'nullable|date|date_format:Y-m-d',
            'dates.due_date' => 'nullable|date|date_format:Y-m-d',
        ];
    }

    public function failedValidation($validator)
    {
        throw new ValidationException($validator, CustomValidationService::error_messages($this->rules(), $validator));
    }
}