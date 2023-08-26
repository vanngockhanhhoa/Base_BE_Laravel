<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Utils\MessageCommon;
use Illuminate\Http\Response;

class EnsureAccountIsValid
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, string $guard)
    {
        $user = Auth::user();
        if($user->is_reload){
            $user->is_reload = false;
            $user->save();
            Auth::logout();
            return response()->json([
                'message' => MessageCommon::MS01_005,
                'user' => $user,
            ], Response::HTTP_FORBIDDEN);
            
        }
        return $next($request);
    }
}
