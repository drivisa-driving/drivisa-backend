<?php

namespace Modules\Drivisa\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ResetPickDropRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'lesson_id' => 'required',
            'new_pickup_point' => 'required',
            'new_dropoff_point' => 'required',
            'difference' => 'required',
            'amount' => 'required',
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
