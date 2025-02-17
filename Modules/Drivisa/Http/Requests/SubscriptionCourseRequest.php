<?php

namespace Modules\Drivisa\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubscriptionCourseRequest extends FormRequest
{
    public function rules()
    {
        return [
            'payment_method_id' => 'required',
        ];
    }

    public function authorize()
    {
        return true;
    }
}