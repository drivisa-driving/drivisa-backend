<?php

namespace Modules\Drivisa\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SavedLocationCreateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'source_latitude' => 'required',
            'source_longitude' => 'required',
            'source_address' => 'required',
            'destination_latitude' => 'required',
            'destination_longitude' => 'required',
            'destination_address' => 'required',
            'default' => ['required', Rule::in(['yes', 'no'])],
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
}
