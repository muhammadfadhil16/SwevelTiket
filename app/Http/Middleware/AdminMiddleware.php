<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || $user->role !== 'Admin') {
            return redirect()->route('catalogue.index')->with([
                'status' => 'error',
                'message' => 'Anda tidak memiliki akses ke halaman admin.',
            ]);
        }

        return $next($request);
    }
}
