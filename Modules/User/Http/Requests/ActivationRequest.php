<?php

namespace Modules\User\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ActivationRequest extends FormRequest
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
            'username.required' => trans('core::core.validation.field_name_required', ['field' => trans('user::users.form.username')]),
            'code.required' => trans('core::core.validation.field_name_required', ['field' => trans('user::users.form.activation_code')]),
        ];
    }
}
