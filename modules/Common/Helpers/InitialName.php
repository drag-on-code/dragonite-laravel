<?php

namespace Dragonite\Common\Helpers;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class InitialName
{
    public static $name;

    public static function make($name, $length = 2, $uppercase = false, $ascii = false, $rtl = false)
    {
        self::setName($name, $ascii);

        $words = new Collection(explode(
            ' ',
            preg_replace(
                '/[^A-Za-z0-9]/',
                '',
                self::$name
            )
        ));

        if ($words->count() === 1) {
            $initial = self::getInitialFromOneWord($words, $length);
        } else {
            $initial = self::getInitialFromMultipleWords($words, $length);
        }

        if ($uppercase) {
            $initial = strtoupper($initial);
        }

        if ($rtl) {
            return collect(mb_str_split($initial))->reverse()->implode('');
        }

        return $initial;
    }

    protected static function setName($name, $ascii)
    {
        throw_if(is_array($name), new \InvalidArgumentException('Passed value cannot be an array'));

        throw_if(is_object($name) && ! method_exists($name, '__toString'), new \InvalidArgumentException('Passed object must have a __toString method'));

        if (filter_var($name, FILTER_VALIDATE_EMAIL)) {
            $name = str_replace('.', ' ', Str::before($name, '@'));
        }

        if ($ascii) {
            $name = Str::ascii($name);
        }

        self::$name = $name;
    }

    protected static function getInitialFromOneWord($words, $length)
    {
        $initial = (string) $words->first();

        if (strlen(self::$name) >= $length) {
            $initial = Str::substr(self::$name, 0, $length);
        }

        return str::upper($initial);
    }

    protected static function getInitialFromMultipleWords($words, $length)
    {
        $initials = new Collection;
        $words->each(function ($word) use ($initials) {
            $initials->push(Str::substr($word, 0, 1));
        });

        return self::selectInitialFromMultipleInitials($initials, $length);
    }

    protected static function selectInitialFromMultipleInitials($initials, $length)
    {
        $initial = $initials->slice(0, $length)->implode('');
        if (Str::length($initial) < $length) {
            $rest = $length - Str::length($initial);
            $initial .= substr(self::$name, $rest * -1, $rest);
        }

        return str::upper($initial);
    }
}
