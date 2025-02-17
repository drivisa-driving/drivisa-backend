<?php namespace Modules\Drivisa\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTraineeProfileRequest extends FormRequest
{
    public function rules()
    {
        return [
            'first_name' => 'min:2',
            'last_name' => 'min:2',
            'address' => 'max:255',
            'phone_number' => 'max:18',
            'city' => 'max:255',
            'postal_code' => 'max:255',
            'province' => 'max:255',
            'bio' => 'max:1000',
            'birth_date' => "date_format:Y-m-d",
            'licence_type' => "min:2|max:8",
            'licence_start_date' => "required_with:licence_end_date|date_format:Y-m-d",
            'licence_end_date' => "date_format:Y-m-d|after:licence_start_date",
        ];
    }

    public function authorize()
    {
        return true;
    }

    public function messages()
    {
        return [
            'first_name.min' => trans('drivisa::drivisa.validation.field_min_chars', [
                'field' => trans('user::users.form.first_name'),
                'count' => 2,
            ]),
            'last_name.min' => trans('drivisa::drivisa.validation.field_min_chars', [
                'field' => trans('user::users.form.last_name'),
                'count' => 2,
            ]),
            'bio.max' => trans('drivisa::drivisa.validation.field_max_chars', [
                'field' => trans('drivisa::drivisa.form.bio'),
                'count' => 1000,
            ]),
            'birth_date.date_format' => trans('drivisa::drivisa.validation.field_date_format', [
                'field' => trans('drivisa::drivisa.form.birth_date')
            ]),
            'licence_start_date.date_format' => trans('drivisa::drivisa.validation.field_date_format', [
                'field' => trans('drivisa::drivisa.form.licence_start_date')
            ]),
            'licence_start_date.required_with' => trans('drivisa::drivisa.validation.field_required_with', [
                'field' => trans('drivisa::drivisa.form.licence_start_date')
            ]),
            'licence_end_date.date_format' => trans('drivisa::drivisa.validation.field_date_format', [
                'field' => trans('drivisa::drivisa.form.licence_end_date')
            ]),
            'licence_end_date.after' => trans('drivisa::drivisa.validation.field_date_after', [
                'field' => trans('drivisa::drivisa.form.licence_start_date')
            ]),
            'licence_type.min' => trans('drivisa::drivisa.validation.field_min_chars', [
                'field' => trans('drivisa::drivisa.form.licence_type'),
                'count' => 2,
            ]),
            'licence_type.max' => trans('drivisa::drivisa.validation.field_max_chars', [
                'field' => trans('drivisa::drivisa.form.licence_type'),
                'count' => 8,
            ]),
        ];
    }
}