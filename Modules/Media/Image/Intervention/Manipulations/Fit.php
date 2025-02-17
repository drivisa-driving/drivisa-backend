<?php

namespace Modules\Media\Image\Intervention\Manipulations;

use Intervention\Image\Image;
use Modules\Media\Image\ImageHandlerInterface;

class Fit implements ImageHandlerInterface
{
    private $defaults = [
        'width' => 100,
        'height' => null,
        'position' => 'center',
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

        return $image->fit($options['width'], $options['height'], $callback, $options['position']);
    }
}
