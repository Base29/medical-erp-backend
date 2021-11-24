<?php

namespace App\Http\Requests\WorkPattern;

use App\Helpers\CustomValidationService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class CreateWorkPatternRequest extends FormRequest
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
            'name' => 'required|string|unique:work_patterns,name',
            'work_timings' => 'required|array',
            'work_timings.start_time' => 'required|string',
            'work_timings.end_time' => 'required|string',
            'working_timings.break_time' => 'required|string',
            'work_timings.repeat_days' => 'required|string',
        ];
    }

    public function messages()
    {
        return [
            'work_timings.required' => 'The work_timings array is required and should not be empty.',
            'work_timings.start_time.required' => 'The start_time field is missing in a object of work_timings',
            'work_timings.end_time.required' => 'The end_time field is missing in a object of work_timings',
            'work_timings.break_time.required' => 'The break_time field is missing in a object of work_timings',
            'work_timings.repeat_time.required' => 'The repeat_time field is missing in a object of work_timings',
        ];
    }

    protected function failedValidation($validator)
    {
        throw new ValidationException($validator, CustomValidationService::error_messages($this->rules(), $validator));
    }
}