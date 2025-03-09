<?php

namespace Dragonite\Common\Helpers\Traits;

trait Middleware
{
    public function middlewareGlobal(): ?array
    {
        return [
            // \App\Http\Middleware\TrustHosts::class,
            \App\Http\Middleware\TrustProxies::class,
            \Illuminate\Http\Middleware\HandleCors::class,
            \App\Http\Middleware\PreventRequestsDuringMaintenance::class,
            \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
            \App\Http\Middleware\TrimStrings::class,
            \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,

            // Dragonite Framework
            \Dragonite\Common\Middleware\Localization::class,
            \Dragonite\Common\Middleware\SanitizeRequest::class,
            \Dragonite\Common\Middleware\DBTransaction::class,
        ];
    }

    public function middlewareAlias(): ?array
    {
        return [
            'auth' => \App\Http\Middleware\Authenticate::class,
            'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
            'auth.session' => \Illuminate\Session\Middleware\AuthenticateSession::class,
            'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
            'can' => \Illuminate\Auth\Middleware\Authorize::class,
            'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
            'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
            'signed' => \App\Http\Middleware\ValidateSignature::class,
            'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
            // 'throttle' => \Dragonite\Common\Middleware\CustomThrottleRequests::class,

            'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,

            // Spatie Laravel Permission
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,

            // JWT Middleware
            // 'jwt.auth' => \Tymon\JWTAuth\Middleware\GetUserFromToken::class,
            // 'jwt.refresh' => \Tymon\JWTAuth\Middleware\RefreshToken::class,

            // Custom IDS Framework
            'none' => \Dragonite\Common\Middleware\None::class,
            'has.jwt' => \Dragonite\Common\Middleware\HasJWTMiddleware::class,
            'jwt.verify' => \Dragonite\Common\Middleware\JWTMiddleware::class,
            // 'jwt.log' => \Dragonite\Common\Middleware\RestApiLogMiddleware::class,

        ];
    }

    public function middlewareApi(): ?array
    {

        return [
            'throttle:api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,

            // IDS Framework
            \Dragonite\Common\Middleware\ConvertRequestToSnakeCase::class,
            \Dragonite\Common\Middleware\ConvertResponseToCamelCase::class,
            \Dragonite\Common\Middleware\EncryptResponse::class,
            \Dragonite\Common\Middleware\HmacValidation::class,
            \Dragonite\Common\Middleware\SetNode::class,
            \Dragonite\Common\Middleware\MobileVersionCheck::class,
        ];
    }

    public function middlewareWeb(): ?array
    {
        return [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ];
    }
}
