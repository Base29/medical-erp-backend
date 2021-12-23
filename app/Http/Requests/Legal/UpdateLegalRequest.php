<?php

namespace App\Http\Requests\Legal;

use App\Helpers\CustomValidationService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class UpdateLegalRequest extends FormRequest
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
            'legal' => 'required|numeric|exists:legals,id',
            'name' => 'exclude_if:is_nurse,0|nullable|string',
            'location' => 'exclude_if:is_nurse,0|nullable|string|',
            'expiry_date' => 'exclude_if:is_nurse,0|nullable|date|date_format:Y-m-d',
            'registration_status' => 'exclude_if:is_nurse,0|nullable|string',
            'register_entry' => 'exclude_if:is_nurse,0|nullable|string',
            'register_entry_date' => 'exclude_if:is_nurse,0|nullable|date|date_format:Y-m-d',
            'nmc_document' => 'exclude_if:is_nurse,0|nullable|file|mimes:png,jpg,doc,docx,pdf|max:5000',
            'nmc_qualifications' => 'exclude_if:is_nurse,0|nullable|array',
            'gmc_reference_number' => 'exclude_if:is_nurse,1|nullable|string',
            'gp_register_date' => 'exclude_if:is_nurse,1|nullable|date|date_format:Y-m-d',
            'provisional_registration_date' => 'exclude_if:is_nurse,1|nullable|date|date_format:Y-m-d',
            'full_registration_date' => 'exclude_if:is_nurse,1|nullable|date|date_format:Y-m-d',
            'gmc_specialist_registers' => 'exclude_if:is_nurse,1|nullable|array',
        ];
    }

    public function failedValidation($validator)
    {
        throw new ValidationException($validator, CustomValidationService::error_messages($this->rules(), $validator));
    }
}