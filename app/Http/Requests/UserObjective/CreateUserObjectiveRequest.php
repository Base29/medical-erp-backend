<?php

namespace App\Http\Requests\UserObjective;

use App\Helpers\CustomValidationService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class CreateUserObjectiveRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'appraisal' => 'required|numeric|exists:appraisals,id',
            'objectives' => 'required|array',
            'objectives.*.objective' => 'required|string|max:1000',
            'objectives.*.status' => [
                'nullable',
                'numeric',
                Rule::in([
                    0, // Incomplete
                    1, // Completed
                ]),
            ],
            'objectives.*.due_date' => 'required|date|date_format:Y-m-d',
        ];
    }

    public function failedValidation($validator)
    {
        throw new ValidationException($validator, CustomValidationService::error_messages($this->rules(), $validator));
    }
}