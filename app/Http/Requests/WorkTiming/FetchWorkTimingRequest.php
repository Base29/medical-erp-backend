<?php

namespace App\Http\Requests\WorkTiming;

use App\Helpers\CustomValidationService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class FetchWorkTimingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'work_pattern' => 'required|numeric|exists:work_patterns,id',
        ];
    }

    public function messages()
    {
        return [
            'work_pattern.required' => 'The work_pattern field is required.',
        ];
    }

    protected function failedValidation($validator)
    {
        throw new ValidationException($validator, CustomValidationService::error_messages($this->rules(), $validator));
    }
}