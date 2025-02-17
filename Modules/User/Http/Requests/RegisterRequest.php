<?php

namespace Modules\User\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\User\Entities\Sentinel\User;

class RegisterRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'first_name' => 'required|min:2|max:255',
            'last_name' => 'required|min:2|max:255',
            'user_type' => ['required', Rule::in(array_values(User::USER_TYPES))],
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed|min:6|max:255',
            'refer_code' => 'numeric|nullable',
            'from_hear' => 'nullable'
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
            'first_name.required' => trans('core::core.validation.field_name_required', ['field' => trans('user::users.form.first_name')]),
            'first_name.min' => trans('core::core.validation.field_min_chars', [
                'field' => trans('user::users.form.first_name'),
                'count' => 2,
            ]),
            'last_name.required' => trans('core::core.validation.field_name_required', ['field' => trans('user::users.form.last_name')]),
            'last_name.min' => trans('core::core.validation.field_min_chars', [
                'field' => trans('user::users.form.last_name'),
                'count' => 2,
            ]),
            'email.required' => trans('core::core.validation.field_name_required', ['field' => trans('user::users.form.email')]),
            'email.email' => trans('core::core.validation.field_name_email', ['field' => trans('user::users.form.email')]),
            'email.unique' => trans('user::users.validation.email_unique'),
            'password.required' => trans('core::core.validation.field_name_required', ['field' => trans('user::users.form.password')]),
            'password.min' => trans('core::core.validation.field_min_chars', [
                'field' => trans('user::users.form.password'),
                'count' => 6,
            ]),
        ];
    }
}
