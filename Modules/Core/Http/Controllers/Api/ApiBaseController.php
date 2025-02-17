<?php
/**
 * Created by PhpStorm.
 * User: maher
 * Date: 12/8/17
 * Time: 2:16 AM
 */

namespace Modules\Core\Http\Controllers\Api;

use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\User\Repositories\UserTokenRepository;

abstract class ApiBaseController extends Controller
{
    protected function getUserFromRequest($request)
    {
        $accessToken = str_replace('Bearer ', '', $request->header('Authorization'));
        $token = app(UserTokenRepository::class)->findByAttributes(['access_token' => $accessToken]);
        return $token ? $token->user : null;
    }

    protected function errorMessage($message, $code = Response::HTTP_INTERNAL_SERVER_ERROR)
    {
        $code = $code != 0 ? $code : Response::HTTP_INTERNAL_SERVER_ERROR;
        return response()->json(['message' => $message], $code);
    }

    protected function successMessage($message, $code = Response::HTTP_OK)
    {
        return response()->json(['message' => $message], $code);
    }
}