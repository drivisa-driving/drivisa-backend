<?php

namespace Modules\Drivisa\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddCreditRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'trainee_id' => 'required',
            'package_id' => 'required',
            'credit' => 'required',
            'payment_intent_id' => 'required',
            'payment_date' => 'required'
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
            'trainee_id.required' => 'Please Select Trainee',
            'package_id.required' => 'Please Select Package',
        ];
    }
}
