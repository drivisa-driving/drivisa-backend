<?php

namespace Modules\User\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Media\Validators\MaxFolderSizeRule;

class UpdateUserProfilePictureRequest extends FormRequest
{
    public function rules()
    {
        $extensions = 'mimes:' . str_replace('.', '', config('ceo.media.config.allowed-types'));
        $maxFileSize = $this->getMaxFileSizeInKilobytes();
        return [
            'picture' => [
                'required',
                new MaxFolderSizeRule(),
                $extensions,
                "max:$maxFileSize",
            ],
            'zone' => [
                'required',
                Rule::in(['profile_picture', 'cover_picture']),
            ],
        ];
    }

    private function getMaxFileSizeInKilobytes()
    {
        return $this->getMaxFileSize() * 1000;
    }

    private function getMaxFileSize()
    {
        return config('ceo.media.config.max-file-size');
    }

    public function messages()
    {
        $size = $this->getMaxFileSize();
        return [
            'file.max' => trans('media::media.file too large', ['size' => $size]),
        ];
    }

    public function authorize()
    {
        return true;
    }
}
