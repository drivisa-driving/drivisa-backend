<?php

namespace Modules\Drivisa\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Drivisa\Entities\WorkingHour;

class UpdateWorkingHourRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    function rules()
    {
        return [
            'status' => Rule::in(array_values(WorkingHour::STATUS)),
            'open_at' => 'date_format:H:i',
            'close_at' => 'required_with:open_at|after:open_at|date_format:H:i',
            'point_id' => 'exists:Modules\Drivisa\Entities\Point,id',
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
