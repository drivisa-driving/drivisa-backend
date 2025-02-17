<?php

namespace Modules\Media\Image\Intervention;

use Modules\Media\Image\ImageFactoryInterface;
use Modules\Media\Image\ImageHandlerInterface;

class InterventionFactory implements ImageFactoryInterface
{
    /**
     * @param string $manipulation
     * @return ImageHandlerInterface
     */
    public function make($manipulation)
    {
        $class = 'Modules\\Media\\Image\\Intervention\\Manipulations\\' . ucfirst($manipulation);

        return new $class();
    }
}
