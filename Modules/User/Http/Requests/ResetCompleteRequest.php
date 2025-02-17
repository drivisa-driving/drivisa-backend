<?php

namespace Modules\User\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ResetCompleteRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'username' => 'required',
            'code' => 'required',
            'password' => 'required|min:6|confirmed',
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
            'user_id.required' => trans('core::core.validation.field_name_required', ['field' => trans('user::auth.form.user_id')]),
            'code.required' => trans('core::core.validation.field_name_required', ['field' => trans('user::auth.form.code')]),
            'password.required' => trans('core::core.validation.field_name_required', ['field' => trans('user::auth.form.password')]),
            'password.confirmed' => trans('user::users.validation.confirmed', ['field' => trans('user::auth.form.password')]),
            'password.min' => trans('core::core.validation.field_min_chars', [
                'field' => trans('user::auth.form.password'),
                'count' => 6,
            ]),
        ];
    }
}
