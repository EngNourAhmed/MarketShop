<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCustomerRole
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            abort(403);
        }

        $hasAnyPermission = $user->permissionItems()->exists();
        $desiredRole = $hasAnyPermission ? 'admin' : 'customer';
        if (($user->role ?? null) !== $desiredRole) {
            $user->forceFill(['role' => $desiredRole])->save();
        }

        if (($user->role ?? null) !== 'customer') {
            abort(403);
        }

        return $next($request);
    }
}
