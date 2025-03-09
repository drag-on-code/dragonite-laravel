<?php

namespace Dragonite\Accessibility\Models;

use Illuminate\Database\Eloquent\Model;

// use Dragonite\Accessibility\Database\Factories\OauthRefreshTokenFactory;

class OAuthRefreshToken extends Model
{
    protected $table = 'oauth_refresh_tokens';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    // protected static function newFactory(): OauthRefreshTokenFactory
    // {
    //     // return OauthRefreshTokenFactory::new();
    // }
}
