<?php

namespace Modules\Drivisa\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\User\Repositories\UserTokenRepository;

class EnsureUserIsSuperAdmin
{

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $this->getUserFromRequest($request);
        if (!$user) {
            $message = trans('user::users.messages.user_not_found');
            return response()->json(['message' => $message], Response::HTTP_NOT_FOUND);
        } else if (!$user->hasRoleSlug('super-admin')) {
            return response()->json(['message' => 'unauthorized'], Response::HTTP_UNAUTHORIZED);
        }
        return $next($request);
    }

    protected function getUserFromRequest($request)
    {
        $accessToken = str_replace('Bearer ', '', $request->header('Authorization'));
        $token = app(UserTokenRepository::class)->findByAttributes(['access_token' => $accessToken]);
        return $token ? $token->user : null;
    }
}
