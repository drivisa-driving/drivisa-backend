<?php

namespace Modules\Drivisa\Exceptions;

use Exception;
use Illuminate\Http\Response;

class InstructorNotFoundException extends Exception
{
    protected $code = Response::HTTP_NOT_FOUND;
}
