<?php

namespace Modules\User\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChangePasswordRequest extends FormRequest
{
    public function rules()
    {
        return [
            'current_password' => 'required',
            'new_password' => 'required|min:3|confirmed',
        ];
    }

    public function authorize()
    {
        return true;
    }

    public function messages()
    {
        return [
            'current_password.required' => trans('core::core.validation.field_name_required',
                ['field' => trans('user::users.form.current_password')]),
            'new_password.required' => trans('core::core.validation.field_name_required',
                ['field' => trans('user::users.form.new_password')]),
            'new_password.min' => trans('core::core.validation.field_min_chars', [
                'field' => trans('user::users.form.new_password'),
                'count' => 6,
            ]),
            'new_password.confirmed' => trans('user::users.validation.confirmed', ['field' => trans('user::users.form.new_password')])
        ];
    }
}
