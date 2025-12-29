<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RequireTwoFactorAuthentication
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user && !$user->two_factor_confirmed_at) {
            $allowedRoutes = [
                'two-factor.login',
                'two-factor.enable',
                'two-factor.confirm',
                'two-factor.qr-code',
                'two-factor.secret-key',
                'two-factor.recovery-codes',
                'two-factor.disable',
                'logout',
                'profile.edit',
                'profile.update',
                'profile.password.update',
            ];

            $routeName = $request->route()?->getName();
            $path = $request->path();

            $allowedPaths = [
                'two-factor-challenge',
                'user/two-factor-authentication',
                'user/two-factor-qr-code',
                'user/two-factor-secret-key',
                'user/two-factor-recovery-codes',
                'user/confirmed-two-factor-authentication',
                'user/confirm-password',
                'profile',
                'logout',
            ];

            $isAllowedPath = collect($allowedPaths)->contains(function ($allowedPath) use ($path) {
                return str_starts_with($path, $allowedPath);
            });

            if ($routeName && !in_array($routeName, $allowedRoutes) && !$isAllowedPath) {
                return redirect()->route('profile.edit')
                    ->with('error', 'É obrigatório configurar a autenticação de dois fatores antes de continuar.');
            }
        }

        return $next($request);
    }
}

