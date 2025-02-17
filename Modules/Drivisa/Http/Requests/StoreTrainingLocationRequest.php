<?php

namespace Modules\Drivisa\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Drivisa\Rules\ValidateLatitudeCoordinate;
use Modules\Drivisa\Rules\ValidateLongitudeCoordinate;

class StoreTrainingLocationRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'source_address' => 'required|max:255',
            'source_latitude' => ['required', new ValidateLatitudeCoordinate],
            'source_longitude' => ['required', new ValidateLongitudeCoordinate],
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
