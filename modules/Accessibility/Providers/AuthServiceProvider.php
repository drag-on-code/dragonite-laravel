<?php

namespace Dragonite\Accessibility\Providers;

use Dragonite\Accessibility\Models\OAuthAccessToken;
use Dragonite\Accessibility\Models\OAuthAuthCode;
use Dragonite\Accessibility\Models\OAuthClient;
use Dragonite\Accessibility\Models\OAuthPersonalAccessClient;
use Dragonite\Accessibility\Models\OAuthRefreshToken;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Passport::ignoreRoutes();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Passport::hashClientSecrets();
        Passport::tokensExpireIn(now()->addDays(15));
        Passport::refreshTokensExpireIn(now()->addDays(30));
        Passport::personalAccessTokensExpireIn(now()->addMonths(6));

        Passport::useTokenModel(OAuthAccessToken::class);
        Passport::useRefreshTokenModel(OAuthRefreshToken::class);
        Passport::useAuthCodeModel(OAuthAuthCode::class);
        Passport::useClientModel(OAuthClient::class);
        Passport::usePersonalAccessClientModel(OAuthPersonalAccessClient::class);

    }
}
