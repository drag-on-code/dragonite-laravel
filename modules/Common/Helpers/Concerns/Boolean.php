<?php

namespace Dragonite\Common\Helpers\Concerns;

use Illuminate\Support\Str;

trait Boolean
{
    public function boolval($payload): ?bool
    {
        if (! is_null($payload)) {
            $payload = Str::lower($payload);
        }

        if ($payload == 'all') {
            return null;
        }

        if ($payload == 'true') {
            return true;
        }

        if ($payload == 'false') {
            return false;
        }

        return boolval($payload);
    }

    public function isBoolean($payload)
    {
        return match ($payload) {
            'true' => true,
            'false' => true,
            default => is_bool($payload),
        };
    }
}
