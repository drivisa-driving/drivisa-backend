<?php

namespace Modules\Media\Image\Intervention\Manipulations;

use Intervention\Image\Image;
use Modules\Media\Image\ImageHandlerInterface;

class Colorize implements ImageHandlerInterface
{
    private $defaults = [
        'red' => 100,
        'green' => 100,
        'blue' => 100,
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

        return $image->colorize($options['red'], $options['green'], $options['blue']);
    }
}
