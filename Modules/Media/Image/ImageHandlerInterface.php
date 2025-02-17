<?php

namespace Modules\Media\Image;

use Intervention\Image\Image;

interface ImageHandlerInterface
{
    /**
     * Handle the image manipulation request
     * @param Image $image
     * @param array $options
     * @return Image
     */
    public function handle($image, $options);
}
