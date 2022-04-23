<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;

class FortifyUIServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        Fortify::loginView(static function () {
            return view('auth.login');
        });

        Fortify::registerView(static function () {
            return view('auth.register');
        });

        Fortify::requestPasswordResetLinkView(static function () {
            return view('auth.forgot-password');
        });

        Fortify::resetPasswordView(static function ($request) {
            return view('auth.reset-password', ['request' => $request]);
        });

        // Fortify::verifyEmailView(function () {
        //     return view('auth.verify-email');
        // });

        // Fortify::confirmPasswordView(function () {
        //     return view('auth.confirm-password');
        // });

        // Fortify::twoFactorChallengeView(function () {
        //     return view('auth.two-factor-challenge');
        // });
    }
}
