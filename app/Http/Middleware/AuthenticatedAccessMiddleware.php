<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticatedAccessMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Connexion obligatoire pour acceder a cette partie du site.',
            ], 401);
        }

        return redirect()
            ->to(route('login', [], false))
            ->with('warning', 'Connexion obligatoire pour acceder a cette partie du site.');
    }
}
