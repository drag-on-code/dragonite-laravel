<?php

namespace Dragonite\Common\Helpers\Concerns;

use Dragonite\Common\Traits\WithSelect;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

trait Model
{
    use WithSelect;

    public function getDbColumnName(string $table)
    {
        $hash = md5("DBColumnName_{$table}");
        $columns = Cache::get($hash);
        if (empty($columns)) {
            $columns = Schema::getColumnListing($table);
            Cache::forever($hash, $columns);
        }

        return $columns;
    }

    public function getModelColumnName(EloquentModel $model)
    {
        $table = $model->getModel()->getTable();

        return $this->getDbColumnName($table);
    }

    public function getFillable(EloquentModel $model)
    {
        return $model->getModel()->getFillable();
    }

    public function getFillableWithTrashed(EloquentModel $model): ?array
    {
        return array_merge($this->getFillable($model), ['deleted_at']);
    }

    /**
     * @return mixed[]
     */
    public function constToEnum(array $array): ?array
    {
        $result = [];
        foreach ($array as $value) {
            $result[] = $this->toSelect($value);
        }

        return $result;
    }
}
