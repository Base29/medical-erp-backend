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
            'name' => 'exclude_if:is_nurse,0|required|string',
            'location' => 'exclude_if:is_nurse,0|required|string|',
            'expiry_date' => 'exclude_if:is_nurse,0|required|date|date_format:Y-m-d',
            'registration_status' => 'exclude_if:is_nurse,0|required|string',
            'register_entry' => 'exclude_if:is_nurse,0|required|string',
            'register_entry_date' => 'exclude_if:is_nurse,0|required|date|date_format:Y-m-d',
            'nmc_document' => 'exclude_if:is_nurse,0|required|file|mimes:png,jpg,doc,docx,pdf|max:5000',
            'nmc_qualifications' => 'exclude_if:is_nurse,0|required|array',
            'gmc_reference_number' => 'exclude_if:is_nurse,1|required|string',
            'gp_register_date' => 'exclude_if:is_nurse,1|required|date|date_format:Y-m-d',
            'provisional_registration_date' => 'exclude_if:is_nurse,1|required|date|date_format:Y-m-d',
            'full_registration_date' => 'exclude_if:is_nurse,1|required|date|date_format:Y-m-d',
            'gmc_specialist_registers' => 'exclude_if:is_nurse,1|required|array',
        ];
    }

    public function failedValidation($validator)
    {
        throw new ValidationException($validator, CustomValidationService::error_messages($this->rules(), $validator));
    }
}