<?php

namespace App\Http\Requests\UserObjective;

use App\Helpers\CustomValidationService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class UpdateUserObjectiveRequest extends FormRequest
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
            'user_objective' => 'required|numeric|exists:user_objectives,id',
            'objective' => 'nullable|string|max:1000',
            'status' => [
                'nullable',
                'numeric',
                Rule::in([
                    0, // Incomplete
                    1, // Complete
                ]),
            ],
            'due_date' => 'nullable|date|date_format:Y-m-d',
        ];
    }

    public function failedValidation($validator)
    {
        throw new ValidationException($validator, CustomValidationService::error_messages($this->rules(), $validator));
    }
}