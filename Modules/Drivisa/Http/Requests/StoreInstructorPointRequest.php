<?php

namespace Modules\Drivisa\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Drivisa\Rules\ValidateLatitudeCoordinate;
use Modules\Drivisa\Rules\ValidateLongitudeCoordinate;

class StoreInstructorPointRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'source_name' => 'max:255',
            'source_address' => 'max:255',
            'destination_name' => 'max:255',
            'destination_address' => 'max:255',
            'is_active' => 'boolean',
            'source_latitude' => ['required', new ValidateLatitudeCoordinate],
            'source_longitude' => ['required', new ValidateLongitudeCoordinate],
            'destination_latitude' => ['required', new ValidateLatitudeCoordinate],
            'destination_longitude' => ['required', new ValidateLongitudeCoordinate],
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
