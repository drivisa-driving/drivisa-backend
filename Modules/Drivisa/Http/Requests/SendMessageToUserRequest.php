<?php

namespace Modules\Drivisa\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendMessageToUserRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'selected_option' => 'required',
            'traineeIds' => 'required|array',
            'message' => 'required',
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
            'selected_option.required' => 'Please select an option',
            'traineeIds.required' => 'Please select trainee\'s',
            'message.required' => 'Please enter a message'
        ];
    }
}
