<?php

namespace Modules\Media\Image\Intervention\Manipulations;

use Intervention\Image\Image;
use Modules\Media\Image\ImageHandlerInterface;

class CanvasResize implements ImageHandlerInterface
{
    private $defaults = [
        'width' => 100,
        'height' => 100,
        'anchor' => 'center',
        'relative' => false,
        'bgcolor' => 'rgba(255, 255, 255, 0)',
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

        return $image->resizeCanvas($options['width'], $options['height'], $options['anchor'], $options['relative'], $options['bgcolor']);
    }
}
