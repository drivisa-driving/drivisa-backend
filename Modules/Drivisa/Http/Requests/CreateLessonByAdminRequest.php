<?php

namespace Modules\Drivisa\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateLessonByAdminRequest extends FormRequest
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
            'instructor_id' => 'required',
            'courseType' => 'required',
            'lessonType' => 'required',
            'dateTime' => 'required',
            'duration' => 'required',
            'lessonStatus' => 'required',
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
            'trainee_id.required' => 'Please Select Trainee',
            'instructor_id.required' => 'Please Select Instructor',
            'courseType.required' => 'Please Select Course',
            'dateTime.required' => 'Please Select Date and Time',
            'duration.required' => 'Please Select Duration',
            'lessonStatus.required' => 'Please Select Status',
        ];
    }
}
