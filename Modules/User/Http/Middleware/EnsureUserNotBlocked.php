<?php

namespace Modules\User\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\User\Repositories\UserTokenRepository;

class EnsureUserNotBlocked
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
        if ($user && $user->blocked_at) {
            $message = trans('user::users.messages.user_blocked');
            return response()->json(['message' => $message], Response::HTTP_FORBIDDEN);
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
