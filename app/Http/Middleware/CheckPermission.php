<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    public function handle(Request $request, Closure $next, string $permissionKey): Response
    {
        $user = $request->user();

        if (!$user) {
            abort(403);
        }

        // Admin role bypass removed to allow for limited admins governed by permissions
        // if ($user->role === 'admin') {
        //     return $next($request);
        // }

        $hasPermission = $user->permissionItems()->where('key', $permissionKey)->exists();

        if (!$hasPermission) {
            abort(403);
        }

        return $next($request);
    }
}
