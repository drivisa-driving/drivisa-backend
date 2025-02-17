<?php

namespace Modules\Drivisa\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetExtraDistanceRequest extends FormRequest
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
            'pick_drop' => 'required'
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
            'lesson_id.required' => 'Lesson id is required',
            'pick_drop.required' => 'Pick Drop is required'
        ];
    }
}
