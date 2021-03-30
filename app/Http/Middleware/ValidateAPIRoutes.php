<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ValidateAPIRoutes
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $token = explode(" ", $request->header('Authorization'));
        if ($token[0] !== "Bearer")
            return response(['message' => 'Forbidden'], 403);
        else if ($token[1] !== env('BEARER_TOKEN'))
            return response(['message' => 'Forbidden'], 403);
        else return $next($request);
    }
}
