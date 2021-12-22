<?php

namespace App\Http\Requests\Legal;

use App\Helpers\CustomValidationService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class CreateLegalRequest extends FormRequest
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
            'is_nurse' => 'required|boolean',
            'name' => 'required|string|exclude_if:is_nurse,true',
            'location' => 'required|string|exclude_if:is_nurse,true',
            'expiry_date' => 'required|date|date_format:Y-m-d|exclude_if,is_nurse,true',
            'registration_status' => 'required|string|exclude_if:is_nurse,true',
            'register_entry' => 'required|string|exclude_if:is_nurse,true',
            'nmc_document' => 'required|string|exclude_if:is_nurse,true',
            'gmc_reference_number' => 'required|string|exclude_if:is_nurse,false',
            'gp_register_date' => 'required|date|date_format:Y-m-d|exclude_if:is_nurse,false',
            'specialist_register' => 'required|string|exclude_if:is_nurse,false',
            'provisional_registration_date' => 'required|date|date_format:Y-m-d|exclude_if:is_nurse,false',
            'full_registration_date' => 'required|date|date_format:Y-m-d|exclude_if:is_nurse,false',
        ];
    }

    public function failedValidation($validator)
    {
        throw new ValidationException($validator, CustomValidationService::error_messages($this->rules(), $validator));
    }
}