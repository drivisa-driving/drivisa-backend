<?php

namespace Modules\Media\Image\Intervention\Manipulations;

use Intervention\Image\Image;
use Modules\Media\Image\ImageHandlerInterface;

class LimitColors implements ImageHandlerInterface
{
    private $defaults = [
        'count' => 255,
        'matte' => null,
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

        return $image->limitColors($options['count'], $options['matte']);
    }
}
