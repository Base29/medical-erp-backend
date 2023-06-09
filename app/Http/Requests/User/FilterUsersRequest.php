<?php

namespace App\Http\Requests\User;

use App\Helpers\CustomValidationService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class FilterUsersRequest extends FormRequest
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
            'mobile_phone' => 'nullable|numeric',
            'last_name' => 'nullable|string',
            'email' => 'nullable|email',
            'role' => 'nullable|numeric|exists:roles,id',
            'is_active' => 'nullable|boolean',
            'is_candidate' => 'nullable|boolean',
            'is_hired' => 'nullable|boolean',
            'is_locum' => 'nullable|boolean',
            'location' => 'nullable|numeric|exists:practices,id',
            'roles' => 'nullable|array',
            'locations' => 'nullable|array',
            'is_blacklisted' => 'nullable|boolean',
            'applicant_status' => [
                'nullable',
                Rule::in([
                    0, // Rejected
                    1, // Accepted
                    2, // Referred for 2nd Interview
                ]),
            ],
            'offer_status' => [
                'nullable',
                Rule::in([
                    0, // Rejected/Declined
                    1, // Accepted
                    2, // Made
                    3, // Revised
                    4, // Pending
                    5, // Discarded
                ]),
            ],
            'induction_status' => 'nullable|boolean',
        ];
    }

    public function failedValidation($validator)
    {
        throw new ValidationException($validator, CustomValidationService::error_messages($this->rules(), $validator));
    }
}