<?php

namespace Modules\User\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Media\Validators\MaxFolderSizeRule;

class DeleteUserProfilePictureRequest extends FormRequest
{
    public function rules()
    {
        return [
            'zone' => [
                'required',
                Rule::in(['profile_picture', 'cover_picture']),
            ]
        ];
    }

    public function authorize()
    {
        return true;
    }
}
