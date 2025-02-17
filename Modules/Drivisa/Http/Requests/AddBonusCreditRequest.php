<?php

namespace Modules\Drivisa\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddBonusCreditRequest extends FormRequest
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
            'type' => 'required',
            'credit' => 'required',
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
            'type.required' => 'Please select bonus credit type',
        ];
    }
}
