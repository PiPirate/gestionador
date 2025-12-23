<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response|RedirectResponse
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        $role = strtolower((string) ($user->role ?? ''));

        if (!in_array($role, ['administrador', 'admin'])) {
            abort(403, __('No tienes permisos para acceder a esta página.'));
        }

        return $next($request);
    }
}
