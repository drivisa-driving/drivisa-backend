<?php

namespace Modules\Drivisa\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SearchLessonsRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'start_at' => 'date_format:Y-m-d',
            'end_at' => 'date_format:Y-m-d',
            'status' => 'Nullable', Rule::in(['reserved', 'inProgress', 'completed', 'canceled']),
            'except' => 'Nullable|string',
            'per_page' => 'Nullable|integer'
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
