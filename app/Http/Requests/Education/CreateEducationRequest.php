<?php

namespace App\Http\Requests\Education;

use App\Helpers\CustomValidationService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class CreateEducationRequest extends FormRequest
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
            'institution' => 'required|string',
            'subject' => 'required|string',
            'start_date' => 'required|date|date_format:Y-m-d',
            'completion_date' => 'required|date|date_format:Y-m-d',
            'degree' => 'required|string',
            'grade' => 'required|string',
            'certificate' => 'required|file|mimes:png,jpg,docx,doc,pdf',
        ];
    }

    public function failedValidation($validator)
    {
        throw new ValidationException($validator, CustomValidationService::error_messages($this->rules(), $validator));
    }
}