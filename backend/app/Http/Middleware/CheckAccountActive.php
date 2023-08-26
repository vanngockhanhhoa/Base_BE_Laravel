<?php

namespace App\Http\Middleware;

use App\Utils\MessageCommon;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class CheckAccountActive
{

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param \Closure(Request): (Response|RedirectResponse) $next
     * @param string $role
     * @return Response|RedirectResponse
     */
    public function handle(Request $request, Closure $next, $role = 'admin')
    {
        $user = JWTAuth::user();

        if ($user->getTable() === 'admins') {
            $user->role = 'admin';
        }

        if (!$user->is_active) {
            return response()->json([
                'message' => MessageCommon::MS01_005,
                'user' => $user,
            ], Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
