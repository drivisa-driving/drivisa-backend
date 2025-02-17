<?php

namespace Modules\Drivisa\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Media\Validators\MaxFolderSizeRule;

class UpdateCarRequest extends FormRequest
{
    public function rules()
    {
        $extensions = 'mimes:' . str_replace('.', '', config('ceo.media.config.allowed-types'));
        $maxFileSize = $this->getMaxFileSizeInKilobytes();
        return [
            'make' => 'max:255',
            'model' => 'max:255',
            'generation' => 'max:255',
            'trim' => 'max:255',
            'picture' => [
                new MaxFolderSizeRule(),
                $extensions,
                "max:$maxFileSize",
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