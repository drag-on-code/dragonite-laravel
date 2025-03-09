<?php

namespace Dragonite\Common\Helpers\Concerns;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

trait Router
{
    public function generateQueryRouter($controller, $module): void
    {
        $routes = $this->getFunctions($controller);
        foreach ($routes as $route) {
            $url = Str::of(string: $route)->snake()->slug()->toString();
            Route::get(uri: $url, action: $route)->name(name: "{$module}.{$url}.query");
        }
    }
}
