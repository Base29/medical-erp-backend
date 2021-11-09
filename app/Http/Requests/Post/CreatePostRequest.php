<?php

namespace App\Http\Requests\Post;

use App\Helpers\CustomValidationService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class CreatePostRequest extends FormRequest
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
            'title' => 'required|string',
            'subject' => 'required|string',
            'message' => 'required|string',
            'category' => 'required|string',
            'type' => 'required|string',
            'attachments.*' => 'mimes:doc,docx,pdf,jpg,png,jpeg',
            'is_public' => 'boolean',
            'practice' => 'required|numeric|exists:practices,id',
        ];
    }

    public function failedValidation($validator)
    {
        throw new ValidationException($validator, CustomValidationService::error_messages($this->rules(), $validator));
    }
}