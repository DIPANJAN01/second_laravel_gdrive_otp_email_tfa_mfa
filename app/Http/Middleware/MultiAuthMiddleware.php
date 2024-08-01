<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class MultiAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Log::info("In multi-auth");
        // if (Auth::guard('tutor')->check() || Auth::guard('web')->check()) {
        //     return $next($request);
        // }
        if (Auth::guard('tutor')->check()) {
            Auth::setUser(Auth::guard('tutor')->user()); //this is super important!!! Otherwise authorization policies won't work!!(Gate::authorize() calls will fail instantly without even executing ANY of its inner methods like before(), etc. then)
            return $next($request);
        } elseif (Auth::guard('web')->check()) {
            Auth::setUser(Auth::guard('web')->user()); //this is super important!!! Otherwise authorization policies won't work!!(Gate::authorize() calls will fail instantly without even executing ANY of its inner methods like before(), etc. then)
            return $next($request);
        }
        // else {//don't need else here, because above, you're not just calling next(), you're RETURNING the result of next(), so anything below won't even follow

        return response()->json(['message' => 'Unauthorized'], 401);
        // }
    }
}
