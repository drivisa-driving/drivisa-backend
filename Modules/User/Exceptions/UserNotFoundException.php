<?php

namespace Modules\User\Exceptions;

use Exception;
use Illuminate\Http\Response;

class UserNotFoundException extends Exception
{
    protected $code = Response::HTTP_NOT_FOUND;
}
