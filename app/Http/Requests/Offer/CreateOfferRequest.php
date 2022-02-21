<?php

namespace App\Http\Requests\Offer;

use App\Helpers\CustomValidationService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class CreateOfferRequest extends FormRequest
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
            'practice' => 'required|numeric|exists:practices,id',
            'hiring_request' => 'required|numeric|exists:hiring_requests,id',
            'user' => 'required|numeric|exists:users,id',
            'work_pattern' => 'required|numeric|exists:work_patterns,id',
            'status' => [
                'required',
                Rule::in(['made', 'accepted', 'declined', 'revised']),
            ],
            'amount' => 'required|string|max:10',
        ];
    }

    public function messages()
    {
        return [
            'status.in' => 'The :attribute is invalid. :attribute can only be made|accepted|declined|revised',
        ];
    }

    public function failedValidation($validator)
    {
        throw new ValidationException($validator, CustomValidationService::error_messages($this->rules(), $validator));
    }
}