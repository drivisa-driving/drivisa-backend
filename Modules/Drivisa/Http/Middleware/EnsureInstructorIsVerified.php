<?php

namespace Modules\Drivisa\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Drivisa\Repositories\InstructorRepository;
use Modules\User\Repositories\UserTokenRepository;

class EnsureInstructorIsVerified
{
    private $instructorRepository;

    public function __construct(InstructorRepository $instructorRepository)
    {
        $this->instructorRepository = $instructorRepository;
    }

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
        } else {
            $instructor = $this->instructorRepository->findByAttributes(['user_id' => $user->id]);
            if (!$instructor) {
                $message = trans('drivisa::drivisa.messages.instructor_not_found');
                return response()->json(['message' => $message], Response::HTTP_NOT_FOUND);
            } elseif (!$instructor->verified) {
                $message = trans('drivisa::drivisa.messages.account_not_verified',);
                return response()->json(['message' => $message], Response::HTTP_FORBIDDEN);
            }
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
