<?php

namespace Modules\Drivisa\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookingLessonByCourseRequest extends FormRequest
{
    public function rules()
    {
        return [
            'working_hours' => 'required|array|min:1',
            'working_hours.*.id' => 'required|exists:Modules\Drivisa\Entities\WorkingHour,id'
        ];
    }

    public function authorize()
    {
        return true;
    }
}