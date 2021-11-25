<?php

namespace App\Http\Requests\WorkTiming;

use App\Helpers\CustomValidationService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class UpdateWorkTimingRequest extends FormRequest
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
            'work_timing' => 'required|numeric|exists:work_timings,id',
            'start_time' => 'string',
            'end_time' => 'string',
            'break_time' => 'numeric',
            'repeat_days' => 'array',
        ];
    }

    public function messages()
    {
        return [
            'work_timing.required' => 'The work_timing field is required.',
        ];
    }

    protected function failedValidation($validator)
    {
        throw new ValidationException($validator, CustomValidationService::error_messages($this->rules(), $validator));
    }
}