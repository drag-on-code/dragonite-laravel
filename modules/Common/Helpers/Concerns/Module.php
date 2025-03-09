<?php

namespace Dragonite\Common\Helpers\Traits;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;
use Nwidart\Modules\Facades\Module as FacadesModule;
use stdClass;

trait Module
{
    public function getModuleVersion(string $moduleName): stdClass
    {
        $lastCommitDate = trim(Process::path(base_path().'/modules/'.$moduleName)->run('git log -1 --format=%cd --date=iso')->output());
        $date = Carbon::parse($lastCommitDate)->format('ymd-Hi');
        $commit = Str::upper(trim(Process::path(base_path().'/modules/'.$moduleName)->run('git describe --always --tags')->output()));
        $data = new stdClass;
        $data->date = $lastCommitDate;
        $data->hash = $commit;
        $data->version = $date;

        return $data;
    }

    public function getModule($force = false)
    {
        $hash = md5('getModule');
        if ($force) {
            Cache::forget($hash);
        }

        $data = Cache::get($hash);
        if (empty($data)) {
            $modules = FacadesModule::all();
            foreach ($modules as $key => $value) {
                $module = FacadesModule::find($key);
                $detail[$key] = [
                    'name' => Str::of($module->getStudlyName())->headline(),
                    'lowerName' => Str::of($module->getStudlyName())->headline()->slug(),
                    'studlyName' => $module->getStudlyName(),
                    'isEnabled' => $module->isEnabled(),
                    'property' => $this->getModuleProperty($module->getStudlyName()),
                    'version' => $this->getModuleVersion($module->getStudlyName()),
                ];
            }

            $data = isset($detail) ? $this->toObject($detail) : collect(null);
            Cache::forever($hash, $data);
        }

        return $data;
    }

    public function getModuleProperty($module)
    {
        $data = collect(json_decode(file_get_contents(base_path().'/modules/'.Str::studly($module).'/composer.json'), true));

        return $data->only(['name', 'description', 'authors', 'icon']);
    }
}
