<?php

namespace Dragonite\Accessibility\Models;

use Illuminate\Database\Eloquent\Model;

// use Dragonite\Accessibility\Database\Factories\OAuthPersonalAccessClientFactory;

class OAuthPersonalAccessClient extends Model
{
    protected $table = 'oauth_personal_access_clients';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    // protected static function newFactory(): OAuthPersonalAccessClientFactory
    // {
    //     // return OAuthPersonalAccessClientFactory::new();
    // }
}
