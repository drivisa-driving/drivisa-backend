<?php

namespace Modules\Drivisa\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetStripeEarningsRequest extends FormRequest
{
    public function rules()
    {
        return [
            'from' => 'date_format:Y-m-d',
            'to' => 'date_format:Y-m-d',
        ];
    }

    public function authorize()
    {
        return true;
    }
}