<?php

namespace App\Http\Requests\Practice;

use App\Helpers\CustomValidationService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class CreatePracticeRequest extends FormRequest
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
            'practice_manager' => 'required|numeric|exists:users,id',
            'name' => 'required|unique:practices,practice_name',
            'logo' => 'nullable|file|mimes:png,jpg',
        ];
    }

    public function messages()
    {
        return [
            'name.unique' => 'Practice with name ' . request()->name . ' already exists',
        ];
    }

    public function failedValidation($validator)
    {
        throw new ValidationException($validator, CustomValidationService::error_messages($this->rules(), $validator));
    }
}