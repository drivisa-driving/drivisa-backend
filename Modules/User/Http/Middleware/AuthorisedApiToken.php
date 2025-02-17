<?php

namespace Modules\User\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\User\Contracts\Authentication;
use Modules\User\Repositories\UserTokenRepository;

class AuthorisedApiToken
{
    /**
     * @var UserTokenRepository
     */
    private $userToken;
    /**
     * @var Authentication
     */
    private $auth;

    public function __construct(UserTokenRepository $userToken, Authentication $auth)
    {
        $this->userToken = $userToken;
        $this->auth = $auth;
    }

    public function handle(Request $request, Closure $next)
    {
        if ($request->header('Authorization') === null) {
            return new Response('Forbidden', 403);
        }

        if ($this->isValidToken($request->header('Authorization')) === false) {
            return new Response('Forbidden', 403);
        }

        return $next($request);
    }

    private function isValidToken($token)
    {
        $found = $this->userToken->findByAttributes(['access_token' => $this->parseToken($token)]);
        if ($found && Carbon::parse($found->expired_at) > now()) {
            $found->update(['last_login' => now()]);
            $this->auth->logUserIn($found->user);
            return true;
        }
        return false;
    }

    private function parseToken($token)
    {
        return str_replace('Bearer ', '', $token);
    }
}
