<?php

namespace Dragonite\Common\Helpers;

use Dragonite\Common\Helpers\Traits\Auth;
use Dragonite\Common\Helpers\Traits\Boolean;
use Dragonite\Common\Helpers\Traits\Middleware;
use Dragonite\Common\Helpers\Traits\Model;
use Dragonite\Common\Helpers\Traits\Module;
use Dragonite\Common\Helpers\Traits\Mutator;
use Dragonite\Common\Helpers\Traits\Odoo;
use Dragonite\Common\Helpers\Traits\Reader;
use Dragonite\Common\Helpers\Traits\Reflector;
use Dragonite\Common\Helpers\Traits\Request;
use Dragonite\Common\Helpers\Traits\Response;
use Dragonite\Common\Helpers\Traits\Router;
use Dragonite\Common\Helpers\Traits\Soketi;
use Illuminate\Support\Str;

class Helper
{
    use Auth,
        Boolean,
        Middleware,
        Model,
        Module,
        Mutator,
        Odoo,
        Reader,
        Reflector,
        Request,
        Response,
        Router,
        Soketi;

    public function isUuid(array|string|null $payload): ?bool
    {
        if (is_array($payload)) {
            foreach ($payload as $value) {
                if (! $this->isUuid($value)) {
                    return false;
                }
            }

            return true;
        }

        return is_string($payload) && Str::isUuid($payload);
    }

    public function versionCompare(string $version1, string $version2, string $operator): ?bool
    {
        return version_compare($version1, $version2, $operator);
    }

    public function versionDifference(string $version1, string $version2): int
    {
        $array1 = Str::remove('v', $version1);
        $array1 = explode('.', $array1);
        $array2 = explode('.', $version2);
        $maxLength = max(count($array1), count($array2));
        $parts1 = array_pad($array1, $maxLength, 0);
        $parts2 = array_pad($array2, $maxLength, 0);
        $normalizeVersion1 = implode('.', $parts1);
        $normalizeVersion2 = implode('.', $parts2);
        $calcVersion1 = (int) str_replace('.', '', $normalizeVersion1);
        $calcVersion2 = (int) str_replace('.', '', $normalizeVersion2);

        return $calcVersion1 - $calcVersion2;
    }
}
