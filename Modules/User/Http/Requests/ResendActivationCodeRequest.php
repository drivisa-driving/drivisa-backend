<?php

namespace Modules\User\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ResendActivationCodeRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email' => 'required_without:username|email',
            'username' => 'required_without:email',
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
            'email.email' => trans('core::core.validation.field_name_email', ['field' => trans('user::users.form.email')]),
            'username.required_without' => trans('user::users.validation.required_without',
                [
                    'field' => trans('user::users.form.username'),
                    'values' => trans('user::users.form.email'),
                ]),
            'email.required_without' => trans('user::users.validation.required_without',
                [
                    'field' => trans('user::users.form.email'),
                    'values' => trans('user::users.form.username'),
                ]),
        ];
    }
}
