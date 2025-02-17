<?php

namespace Modules\Drivisa\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCourseRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name' => 'required|max:255',
            'description' => 'max:1000',
            'cost' => 'required|numeric',
            'cost_lecture' => 'required|numeric',
            'count_hour' => 'required|numeric',
        ];
    }

    public function authorize()
    {
        return true;
    }
}