<?php

namespace Modules\User\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Modules\User\Contracts\Authentication;

class GuestMiddleware
{
    /**
     * @var Authentication
     */
    private $auth;

    public function __construct(Authentication $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($this->auth->check()) {
            return Redirect::route(config('ceo.user.config.redirect_route_after_login'));
        }

        return $next($request);
    }
}
