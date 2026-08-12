<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;

class FortifyUIServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Fortify::loginView(static fn () => view('auth.login'));

        Fortify::registerView(static fn () => view('auth.register'));

        Fortify::requestPasswordResetLinkView(static fn () => view('auth.forgot-password'));

        Fortify::resetPasswordView(static fn ($request) => view('auth.reset-password', ['request' => $request]));

        // Fortify::verifyEmailView(static fn () => view('auth.verify-email'));

        // Fortify::confirmPasswordView(static fn () => view('auth.confirm-password'));

        // Fortify::twoFactorChallengeView(static fn () => view('auth.two-factor-challenge'));
    }
}
