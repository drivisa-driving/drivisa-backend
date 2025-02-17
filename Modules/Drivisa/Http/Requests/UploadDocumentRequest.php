<?php

namespace Modules\Drivisa\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Media\Validators\MaxFolderSizeRule;

class UploadDocumentRequest extends FormRequest
{
    public function rules()
    {
        $extensions = 'mimes:' . str_replace('.', '', config('ceo.media.config.allowed-types'));
        $maxFileSize = $this->getMaxFileSizeInKilobytes();

        return [
            'files' => 'array|required',
            'files.*.file' => ['required', $extensions, "max:$maxFileSize"],
            'files.*.zone' => 'required',
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
