<?php

namespace Modules\Drivisa\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchInstructorsRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'filter' => 'array|min:1',
            'filter.term' => 'string|nullable',
            'filter.make' => 'string|nullable',
            'filter.language' => 'string|nullable',
            'filter.address' => 'string|nullable',
            'filter.date' => 'date_format:Y-m-d|nullable',
            'filter.open_at' => 'date_format:H:i|nullable',
            'filter.close_at' => 'date_format:H:i|nullable',

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
