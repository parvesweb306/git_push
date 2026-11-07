<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureIsAdmin
{
    public function handle(Request $request, Closure $next)
    {
        abort_unless(
            $request->user() && $request->user()->isAdmin(),
            403,
            'শুধুমাত্র Main Admin এই পেজ দেখতে পারবেন।'
        );

        return $next($request);
    }
}