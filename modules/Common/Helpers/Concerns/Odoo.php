<?php

namespace Dragonite\Common\Helpers\Traits;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

trait Odoo
{
    public function transformOdooId(array $fields, $request, $remove = true)
    {
        $request = self::requestToSnake($request);

        $r = [];
        foreach ($fields as $key => $model) {
            $uuid = $this->pluckId($request, $key);
            if (is_array($uuid) && isset($uuid['id'])) {
                $uuid = $uuid['id'];
            }

            if ($this->isUuid($uuid)) {
                $data = $uuid instanceof $model ? $uuid : app($model)->find($uuid);

                if ($data) {
                    $r["{$key}_id"] = $data instanceof \Illuminate\Database\Eloquent\Collection ? $data->pluck('odoo_id')->toArray() : $data->odoo_id;
                    $request->request->remove($key);
                }
            }
        }

        $data = array_merge($request->all(), $r);
        if ($remove) {
            foreach (array_keys($fields) as $key) {
                unset($data[$key]);
            }
        }

        return $request->replace($data);
    }

    public function injectOdooId($request, $mapping)
    {
        foreach ($mapping as $key => $model) {
            $arrayKey = explode('.', $key);
            $data = $request->all();
            $plucked = Arr::pluck([$data], "{$key}.id");
            $ids = Arr::flatten($plucked);
            $models = $model::query()
                ->withoutGlobalScopes()
                ->withTrashed()
                ->select('id', 'odoo_id')
                ->whereIn('id', $ids)->get();
            $last = last($arrayKey);
            array_pop($arrayKey);
            $first = implode('.', $arrayKey);

            foreach (data_get($data, "{$key}") as $dataKey => $value) {
                $value['odoo_id'] = $models->where('id', $value['id'])->first()?->odoo_id;
                $parent = data_get($data, Str::replace('*', $dataKey, $first));
                $parent["{$last}_id"] = $value['odoo_id'];
                data_set($data, Str::replace('*', $dataKey, $first), $parent);
            }

            $request->replace($data);
        }

        return $request;
    }
}
