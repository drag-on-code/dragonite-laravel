<?php

namespace Dragonite\Accessibility\Models;

use Illuminate\Database\Eloquent\Model;

// use Dragonite\Accessibility\Database\Factories\OauthAccessTokenFactory;

class OAuthAccessToken extends Model
{
    protected $table = 'oauth_access_tokens';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    // protected static function newFactory(): OauthAccessTokenFactory
    // {
    //     // return OauthAccessTokenFactory::new();
    // }
}
