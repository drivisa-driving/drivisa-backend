<?php

namespace Modules\Drivisa\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStripeAccountRequest extends FormRequest
{
    public function rules()
    {
        return [
            'email' => 'required|email',
            'first_name' => 'required',
            'last_name' => 'required',
            'id_number' => 'required',
            'phone' => 'required',
            'birth_date' => 'date_format:Y-m-d',
            'address.city' => 'required',
            'address.line1' => 'required',
            'address.postal_code' => 'required',
            'address.state' => 'required',
            'bank_account.country' => 'required',
            'bank_account.currency' => 'required',
            'bank_account.account_holder_name' => 'required',
            'bank_account.account_holder_type' => 'required',
            'bank_account.transit_number' => 'required',
            'bank_account.institution_number' => 'required',
            'bank_account.account_number' => 'required',
        ];
    }

    public function authorize()
    {
        return true;
    }
}