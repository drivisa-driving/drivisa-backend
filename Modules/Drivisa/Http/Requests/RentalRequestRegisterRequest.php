<?php

namespace Modules\Drivisa\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RentalRequestRegisterRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'package_id' => 'required|exists:drivisa__packages,id',
            'location' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
            'booking_date_time' => 'required',
            'instructor_id' => 'required',
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
            'package_id.exists' => "Selected Package is invalid",
            'instructor_id.required' => "Please Select Instructor",
        ];
    }
}
