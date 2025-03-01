<?php

namespace Modules\Media\Image\Intervention\Manipulations;

use Intervention\Image\Image;
use Modules\Media\Image\ImageHandlerInterface;

class Resize implements ImageHandlerInterface
{
    private $defaults = [
        'width' => 200,
        'height' => 200,
    ];

    /**
     * Handle the image manipulation request
     * @param Image $image
     * @param array $options
     * @return Image
     */
    public function handle($image, $options)
    {
        $options = array_merge($this->defaults, $options);

        $callback = isset($options['callback']) ? $options['callback'] : null;

        return $image->resize($options['width'], $options['height'], $callback);
    }
}
