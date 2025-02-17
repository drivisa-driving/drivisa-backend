<?php

namespace Modules\Media\Image\Intervention\Manipulations;

use Intervention\Image\Image;
use Modules\Media\Image\ImageHandlerInterface;

class Blur implements ImageHandlerInterface
{
    private $defaults = [
        'amount' => 1,
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

        return $image->blur($options['amount']);
    }
}
