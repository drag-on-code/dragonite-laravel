<?php

namespace Dragonite\Accessibility\Models;

use Illuminate\Database\Eloquent\Model;

// use Dragonite\Accessibility\Database\Factories\OAuthClientFactory;

class OAuthClient extends Model
{
    protected $table = 'oauth_clients';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    // protected static function newFactory(): OAuthClientFactory
    // {
    //     // return OAuthClientFactory::new();
    // }
}
