<?php

namespace Modules\Drivisa\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCourseRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name' => 'max:255',
            'description' => 'max:1000',
            'cost' => 'numeric',
            'cost_lecture' => 'numeric',
            'count_hour' => 'numeric',
        ];
    }

    public function authorize()
    {
        return true;
    }
}