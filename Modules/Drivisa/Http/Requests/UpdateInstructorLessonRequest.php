<?php

namespace Modules\Drivisa\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInstructorLessonRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'instructor_note' => 'max:1000',
            'instructor_evaluation' => 'array',
            'instructor_evaluation.*.id' => 'exists:Modules\Drivisa\Entities\EvaluationIndicator,id',
            'instructor_evaluation.*.value' => 'required_with:instructor_evaluation.*.id|numeric',
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
