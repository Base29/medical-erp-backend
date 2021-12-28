<?php

namespace App\Http\Requests\Termination;

use App\Helpers\CustomValidationService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class CreateTerminationRequest extends FormRequest
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
            'date' => 'required|date|date_format:Y-m-d',
            'reason' => 'required|string',
            'detail' => 'required|string|max:2000',
            'is_exit_interview_performed' => 'required|boolean',
        ];
    }

    protected function failedValidation($validator)
    {
        throw new ValidationException($validator, CustomValidationService::error_messages($this->rules(), $validator));
    }
}