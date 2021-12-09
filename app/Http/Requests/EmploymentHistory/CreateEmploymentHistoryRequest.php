<?php

namespace App\Http\Requests\EmploymentHistory;

use Illuminate\Foundation\Http\FormRequest;

class CreateEmploymentHistoryRequest extends FormRequest
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
            'employer_name' => 'required|string|max:50',
            'address' => 'required|string',
            'phone_number' => 'required|string',
            'type_of_business' => 'required|string',
            'job_title' => 'required|string',
            'job_start_date' => 'required|date|date_format:Y-m-d',
            'job_end_date' => 'required|date|date_format:Y-m-d',
            'salary' => 'required|string',
            'reporting_to' => 'required|string',
            'period_of_notice' => 'required|string',
            'can_contact_referee' => 'required|boolean',
            'reason_for_leaving' => 'required|string|max:500',
            'responsibilities_duties_desc' => 'required|string|max:500',
            'is_current' => 'required|boolean',
        ];
    }
}