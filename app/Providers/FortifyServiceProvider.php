<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Contracts\LoginViewResponse;
use Laravel\Fortify\Contracts\TwoFactorChallengeViewResponse;
use Inertia\Inertia;

class FortifyServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->app->singleton(LoginViewResponse::class, function () {
            return new class implements LoginViewResponse {
                public function toResponse($request)
                {
                    return Inertia::render('Auth/Login')->toResponse($request);
                }
            };
        });

        $this->app->singleton(TwoFactorChallengeViewResponse::class, function () {
            return new class implements TwoFactorChallengeViewResponse {
                public function toResponse($request)
                {
                    return Inertia::render('Auth/TwoFactorChallenge')->toResponse($request);
                }
            };
        });
    }
}

