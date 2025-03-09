<?php

namespace Dragonite\Common\Helpers\Concerns;

use Dragonite\Common\Helpers\KeyCaseConverter;
use Dragonite\Common\Models\Model;
use Galahad\TimezoneMapper\Facades\TimezoneMapper;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

trait Mutator
{
    public function toArrayKeyCamel(array $array): ?array
    {
        return resolve(KeyCaseConverter::class)->convert(
            case: KeyCaseConverter::CASE_CAMEL,
            data: $array
        );
    }

    public function toArray(object|array $object, $normalize = false): mixed
    {
        $data = json_decode(json_encode($object), true);
        if (! $normalize) {
            return $data;
        }

        return resolve(KeyCaseConverter::class)->convert(
            case: KeyCaseConverter::CASE_SNAKE,
            data: $data
        );
    }

    public function toObject(object|array $array, $normalize = false): mixed
    {
        $data = json_decode(json_encode($array));
        if (! $normalize) {
            return $data;
        }

        return resolve(KeyCaseConverter::class)->convert(
            case: KeyCaseConverter::CASE_CAMEL,
            data: $data
        );
    }

    public function toRoman(int $number): ?string
    {
        $map = ['M' => 1000, 'CM' => 900, 'D' => 500, 'CD' => 400, 'C' => 100, 'XC' => 90, 'L' => 50, 'XL' => 40, 'X' => 10, 'IX' => 9, 'V' => 5, 'IV' => 4, 'I' => 1];
        $returnValue = '';
        while ($number > 0) {
            foreach ($map as $roman => $int) {
                if ($number >= $int) {
                    $number -= $int;
                    $returnValue .= $roman;
                    break;
                }
            }
        }

        return $returnValue;
    }

    public function toGeometry($borderArray, $properties = []): ?array
    {
        // Determine the type based on the structure of the array
        $type = is_array($borderArray[0][0]) ? 'MultiPolygon' : 'Polygon';

        return [
            'type' => 'FeatureCollection',
            'features' => [
                [
                    'type' => 'Feature',
                    'properties' => $properties,
                    'geometry' => [
                        'type' => $type,
                        'coordinates' => [$borderArray],
                    ],
                ],
            ],
        ];
    }

    public function requestToSnake($request): mixed
    {
        $request->replace(
            resolve(KeyCaseConverter::class)->convert(
                case: KeyCaseConverter::CASE_SNAKE,
                data: $request->all()
            )
        );

        return $request;
    }

    public function mutateDateRequest(array $field, $request): mixed
    {
        $r = [];
        foreach ($field as $d) {
            if ($request->{$d}) {
                $r[$d] = strtotime($request->{$d});
            }
        }

        return empty($r) ? $request : $request->merge($r);
    }

    public function mutateBase64Media(array $field, $request): mixed
    {
        $r = [];
        foreach ($field as $d) {
            if ($request->{$d}) {
                $r[$d] = (new self)->readBase64Media($request->{$d});
            }
        }

        return empty($r) ? $request : $request->merge($r);
    }

    public function objectToTimestamp(object $object, $keys): object
    {
        foreach ($keys as $key) {
            if (isset($object->{$key})) {
                $object->{$key} = strtotime($object->{$key});
            }
        }

        return $object;
    }

    public function objectRemoveProperty(object $object, $keys): object
    {
        foreach ($keys as $key) {
            if (property_exists($object, $key)) {
                unset($object->{$key});
            }
        }

        return $object;
    }

    public function objectNormalize(object $object, Model $model): object
    {
        if (! isset($object->created_at)) {
            $object->created_at = Carbon::now();
        }

        if (! isset($object->updated_at)) {
            $object->updated_at = Carbon::now();
        }

        $columns = $this->getModelColumnName($model);

        foreach (array_keys(get_object_vars($object)) as $key) {
            if (! in_array($key, $columns)) {
                unset($object->{$key});
            }
        }

        foreach ($columns as $column) {
            if (! isset($object->{$column})) {
                $object->{$column} = null;
            }
        }

        return $object;
    }

    public function lowerCaseArray($array): ?array
    {
        if (is_array($array)) {
            $results = [];
            foreach ($array as $key => $value) {
                $result = is_array($value) ? $this->lowerCaseArray($value) : Str::lower($value);

                $lowercaseKey = Str::lower($key);
                $results[$lowercaseKey] = $result;
            }

            return $results;
        }

        return null;
    }

    public function arrayToFormattedString($array, $separator = ',', $lastSeparator = '&'): ?string
    {
        if (empty($array)) {
            return null;
        }

        if (count($array) == 1) {
            return $array[0];
        }

        $lastElement = array_pop($array);
        $text = implode("{$separator} ", $array);

        return "{$text} {$lastSeparator} {$lastElement}";
    }

    public function getUnitLevelName($payload): ?string
    {
        return match ($payload) {
            0 => 'Corporate',
            1 => 'Regional Office',
            2 => 'Area',
            3 => 'Branch',
            4 => 'Outlet',
            default => 'Unit',
        };
    }

    public function toCollection($props, $preserve_array_indexes = false): Collection
    {
        $obj = new \stdClass;

        if (! is_array($props)) {
            return collect($props);
        }

        foreach ($props as $key => $value) {

            if (is_numeric($key) && ! $preserve_array_indexes) {
                if (! is_array($obj)) {
                    $obj = [];
                }

                $obj[] = $this->toCollection($value);

                continue;
            }

            $obj->{$key} = is_array($value) ? $this->toCollection($value) : $value;
        }

        return collect($obj);
    }

    public function toIndonesianFormattedDate(?Carbon $date): ?string
    {
        $timezone = $date?->getOffsetString();
        $formattedTimezone = match ($timezone) {
            '+07:00' => '(WIB)',
            '+08:00' => '(WITA)',
            '+09:00' => '(WIT)',
            default => "{$timezone}",
        };

        if ($date) {
            return $date->format('Y-m-d H:i:s')." {$formattedTimezone}";
        }

        return null;
    }

    public function getTimezoneFromLatLong($latitude, $longitude): ?string
    {
        return TimezoneMapper::mapCoordinates($latitude, $longitude, 'Asia/Jakarta');
    }
}
