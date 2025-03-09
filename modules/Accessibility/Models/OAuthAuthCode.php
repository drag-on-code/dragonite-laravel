<?php

namespace Dragonite\Accessibility\Models;

use Illuminate\Database\Eloquent\Model;

// use Dragonite\Accessibility\Database\Factories\OauthAuthCodeFactory;

class OAuthAuthCode extends Model
{
    protected $table = 'oauth_auth_codes';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    // protected static function newFactory(): OauthAuthCodeFactory
    // {
    //     // return OauthAuthCodeFactory::new();
    // }
}
