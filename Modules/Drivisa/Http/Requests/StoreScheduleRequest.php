<?php

namespace Modules\Drivisa\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Drivisa\Entities\WorkingDay;
use Modules\Drivisa\Entities\WorkingHour;

class StoreScheduleRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    function rules()
    {
        return [
            'date' => 'required|date_format:Y-m-d',
            'status' => Rule::in(array_values(WorkingDay::STATUS)),
            'copy' => 'boolean',
            'exclude' => ['array', Rule::in(array_values(WorkingDay::DAYS_OF_WEEK))],
            'from' => 'required_if:copy,1|date_format:Y-m-d',
            'to' => 'required_if:copy,1|date_format:Y-m-d',
            'working_hours' => ['array'],
            'working_hours.*.status' => Rule::in(array_values(WorkingHour::STATUS)),
            'working_hours.*.open_at' => 'required|date_format:H:i',
            'working_hours.*.shift' => 'required|integer|between:60,120',
            'working_hours.*.point_id' => 'exists:Modules\Drivisa\Entities\Point,id',
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
}
