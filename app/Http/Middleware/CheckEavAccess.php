<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckEavAccess
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Kiểm tra quyền truy cập EAV
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // Kiểm tra permission (nếu có)
        // if (!auth()->user()->can('access-eav')) {
        //     abort(403);
        // }

        return $next($request);
    }
}
