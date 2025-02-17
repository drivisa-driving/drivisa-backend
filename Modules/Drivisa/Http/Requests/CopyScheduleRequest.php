<?php

namespace Modules\Drivisa\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Drivisa\Entities\WorkingDay;

class CopyScheduleRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    function rules()
    {
        return [
            'working_day_id' => 'required|exists:Modules\Drivisa\Entities\WorkingDay,id',
            'exclude' => ['array', Rule::in(array_values(WorkingDay::DAYS_OF_WEEK))],
            'from' => 'required|date_format:Y-m-d',
            'to' => 'required|date_format:Y-m-d',
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
