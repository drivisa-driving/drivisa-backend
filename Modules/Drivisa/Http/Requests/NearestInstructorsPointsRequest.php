<?php

namespace Modules\Drivisa\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Drivisa\Rules\ValidateLatitudeCoordinate;
use Modules\Drivisa\Rules\ValidateLongitudeCoordinate;

class NearestInstructorsPointsRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'latitude' => [new ValidateLatitudeCoordinate],
            'longitude' => [new ValidateLongitudeCoordinate],
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
