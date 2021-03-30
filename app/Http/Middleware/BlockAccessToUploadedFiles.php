<?php

namespace App\Http\Middleware;

use App\Http\Controllers\DTS;
use App\Models\Password;
use Closure;
use Illuminate\Http\Request;

class BlockAccessToUploadedFiles
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
        $password = $request->input('password');
        if ($password) {
            $dtsPassword = Password::where('password', $password)->first();
            if (!is_null($dtsPassword)) return $next($request);
            else return response(['message' => 'Unauthorized'], 401);
        } else return response(['message' => 'Unauthorized'], 401);
    }
}
