<?php

namespace Modules\User\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function rules()
    {

        return [
            'first_name' => 'min:2',
            'last_name' => 'min:2',
        ];
    }

    public function authorize()
    {
        return true;
    }

    public function messages()
    {
        return [
            'first_name.min' => trans('core::core.validation.field_min_chars', [
                'field' => trans('user::users.form.first_name'),
                'count' => 2,
            ]),
            'last_name.min' => trans('core::core.validation.field_min_chars', [
                'field' => trans('user::users.form.last_name'),
                'count' => 2,
            ]),
        ];
    }
}
