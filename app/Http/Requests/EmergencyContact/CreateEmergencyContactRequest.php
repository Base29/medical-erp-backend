<?php

namespace App\Http\Requests\EmergencyContact;

use App\Helpers\CustomValidationService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class CreateEmergencyContactRequest extends FormRequest
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
            'user' => 'required|numeric|exists:users,id',
            'name' => 'required|string',
            'relationship' => 'required|string',
            'primary_phone' => 'required|string',
            'secondary_phone' => 'required|string',
        ];
    }

    public function failedValidation($validator)
    {
        throw new ValidationException($validator, CustomValidationService::error_messages($this->rules(), $validator));
    }
}