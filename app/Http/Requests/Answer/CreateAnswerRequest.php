<?php

namespace App\Http\Requests\Answer;

use App\Helpers\CustomValidation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class CreateAnswerRequest extends FormRequest
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
            'answer' => 'required',
            'post' => 'required|numeric|exists:posts,id',
        ];
    }

    protected function failedValidation($validator)
    {
        throw new ValidationException($validator, CustomValidation::error_messages($this->rules(), $validator));
    }
}