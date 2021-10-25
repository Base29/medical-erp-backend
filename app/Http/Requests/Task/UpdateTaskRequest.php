<?php

namespace App\Http\Requests\Task;

use App\Helpers\CustomValidation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class UpdateTaskRequest extends FormRequest
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
            'status' => 'boolean',
            'reason' => 'string|nullable',
            'comment' => 'string|nullable',
            'manager_comment' => 'string|nullable',
            'acknowledgement' => 'boolean',
            'task' => 'required|numeric|exists:tasks,id',
        ];
    }

    public function failedValidation($validator)
    {
        throw new ValidationException($validator, CustomValidation::error_messages($this->rules(), $validator));
    }
}