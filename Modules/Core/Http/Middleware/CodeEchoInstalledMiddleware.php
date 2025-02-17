<?php

namespace Modules\Core\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class CodeEchoInstalledMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     * @throws Exception
     */
    public function handle($request, Closure $next)
    {
        if (!file_exists(base_path('..env')) || !Schema::hasTable('users')) {
            throw new Exception('CodeEcho is not yet installed');
        }

        return $next($request);
    }
}
