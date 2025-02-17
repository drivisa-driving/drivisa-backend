<?php

namespace Modules\Drivisa\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReduceCreditRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'trainee_id' => 'required',
            'course_type' => 'required',
            'credit_reduce' => 'required',
            'note' => 'required',
            'date' => 'required'
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    public function messages()
    {
        return [
            'trainee_id.required' => 'Please select trainee',
            'course_type.required' => 'Please select course',
            'hours_reduce.required' => 'Please select hours to reduce',
            'note.required' => 'Please specify a reason for reducing the credit hour\'s',
            'date.required' => 'Please select date'
        ];
    }
}
