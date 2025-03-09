<?php

namespace Dragonite\Common\Helpers\Concerns;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

trait Request
{
    private function pluckId($request, $key)
    {
        if (is_array($request->{$key}) && isset($request->{$key}['id'])) {
            return $request->{$key} ?: null;
        }

        return $request->{$key};
    }

    public function mapSelect(array $fields, $request)
    {
        $request = self::requestToSnake($request);
        foreach ($fields as $key => $value) {
            $data[$key] = $value['id'];
        }

        return $request->replace($data);
    }

    public function removeObject(array $fields, $request)
    {
        $request = self::requestToSnake($request);
        $r = [];
        foreach ($fields as $key) {
            $r[$key] = $request->has($key) ? $request->{$key}['id'] : null;
        }

        $data = array_merge($request->all(), $r);

        return $request->replace($data);
    }

    public function mergeRequest(array $field, $request)
    {
        $request = self::requestToSnake($request);
        $r = [];
        foreach ($field as $d) {
            $r["{$d}_id"] = is_array($request->{$d}) ? $request->{$d}['id'] : ($request->{$d} ?: null);
            $request->request->remove($d);
        }

        $data = array_merge($request->all(), $r);

        foreach ($field as $d) {
            unset($data[$d]);
        }

        return $request->replace($data);
    }

    public function mapRelation(array $alias, $request)
    {
        $request = self::requestToSnake($request);
        $r = [];
        foreach ($alias as $key => $value) {
            if ($request->{$value} && $request->{$value} != 'null') {
                $r[$key] = $request->{$value};
            }
        }

        return empty($r) ? $request : $request->merge($r);
    }

    public function mapRequest(array $alias, $request)
    {
        $request = self::requestToSnake($request);
        $r = [];
        foreach ($alias as $key => $value) {
            if (! $request[$value]) {
                $r[$value] = $request->{$key};
                $request->request->remove($key);
            }
        }

        $data = array_merge($request->all(), $r);

        return empty($r) ? $request : $request->replace($data);
    }

    public function mapTimeframe(array $timeframe, $request)
    {
        $request = self::requestToSnake($request);
        $r = [];
        foreach ($timeframe as $tf) {
            $range = Str::snake($tf);
            $from = Str::snake($tf.'From');
            $to = Str::snake($tf.'To');
            if ($request->{$from}) {
                $r[$range]['from'] = $request->{$from};
            }

            if ($request->{$to}) {
                $r[$range]['to'] = $request->{$to};
            }
        }

        return empty($r) ? $request : $request->merge($r);
    }

    public function mapFilter(array $timeframe, $request)
    {
        $request = self::requestToSnake($request);
        $r = [];
        $filter = Str::snake($request->input('filter_type'));
        if ($filter) {
            $tf = $filter;
            $range = Str::snake($tf);
            $from = Str::snake($tf.'From');
            $to = Str::snake($tf.'To');

            if ($request->{$from}) {
                $r[$range]['from'] = $request->{$from};
            }

            if ($request->{$to}) {
                $r[$range]['to'] = $request->{$to};
            }
        }

        return empty($r) ? $request : $request->merge($r);
    }

    /**
     * @return mixed[]
     */
    public function transfromRules($rules, $transform = true): ?array
    {
        foreach ($rules as $key => $value) {
            if (! is_array($value)) {
                $value = explode('|', $value);
                $value = array_map(fn ($value) => $transform ? Str::replace('_id', '.id', $value) : $value, $value);

                $rules[$key] = $value;
            }

            if (Str::endsWith($key, '_id') && $key != 'odoo_id') {
                $newKey = $transform ? Str::replace('_id', '.id', $key) : $key;
                $rules[$newKey] = $value;
                unset($rules[$key]);
            }
        }

        ksort($rules);

        $rules = array_map(function ($value) {
            if (is_array($value)) {
                return array_filter($value, fn ($item): ?bool => ! $item instanceof \Illuminate\Validation\Rules\Unique);
            }

            if ($value instanceof \Illuminate\Validation\Rules\Unique) {
                return null;
            }

            return $value;
        }, $rules);

        // Remove null values
        $rules = array_filter($rules, fn ($value): ?bool => $value !== null);

        return $rules;
    }

    public function getClientTimezone(): ?string
    {
        $clientTimezone = (string) request()->header('X-Client-Timezone');
        $clientTimezone = str($clientTimezone)->title()->toString();

        $validator = Validator::make(
            ['client_timezone' => $clientTimezone],
            ['client_timezone' => 'required|timezone'],
        );

        if (empty($validator->valid())) {
            return config('app.timezone');
        }

        return $clientTimezone;
    }

    public function getValidTimezone($timezone = null): ?string
    {
        $timezone = str($timezone)->title()->toString();
        $validator = Validator::make(
            ['timezone' => $timezone],
            ['timezone' => 'required|timezone'],
        );

        if (empty($validator->valid())) {
            return $this->getClientTimezone();
        }

        return $timezone;
    }
}
