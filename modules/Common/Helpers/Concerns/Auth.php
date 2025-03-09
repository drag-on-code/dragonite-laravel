<?php

namespace Dragonite\Common\Helpers\Concerns;

use Dragonite\Common\Models\Permission;
use Dragonite\Common\Models\Role;
use Illuminate\Support\Facades\Auth as FacadesAuth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Request;

trait Auth
{
    public function logAuth($causer, $event, $log): void
    {
        $userAgent = [
            'ip' => Request::ip(),
            'userAgent' => Request::userAgent(),
            'fingerprint' => Request::fingerprint(),
            'secure' => Request::secure(),
            // 'geoip' => geoip(Request::ip()),
        ];

        activity('auth')
            ->On($causer)
            ->By($causer)
            ->withProperties($userAgent)
            ->event($event)
            ->log(implode('|', [$log, Request::ip(), Request::userAgent()]));
    }

    public function getPermissions($roleId = null, $force = false)
    {
        $auth = FacadesAuth::check();
        $hash = $auth ? $roleId.FacadesAuth::id() : $roleId;

        $hash = md5("getPermissions_{$hash}");
        if ($force) {
            Cache::forget($hash);
        }

        $data = Cache::get($hash);
        if (empty($data)) {
            $thisPermission = null;
            if ($roleId) {
                $role = Role::where('id', $roleId)->first();
            }

            $permissions = Permission::query()
                ->where(function ($q) {
                    $q->whereNull('is_hidden');
                    $q->orWhere('is_hidden', false);
                });
            if (! FacadesAuth::user()->hasRole('Root')) {
                $skipedPermissions = ['configuration.module', 'module.accessibility.permission'];
                foreach ($skipedPermissions as $val) {
                    $permissions->where('name', 'not like', "{$val}%");
                }
            }

            $permissions = $permissions->orderBy('name', 'asc')->get();
            foreach ($permissions as $perms) {
                $name = explode('-', $perms->name);
                $permission[$name[0]][$name[1]] = $roleId ? $role->hasPermissionTo($perms->name) : false;
                $thisPermission[$perms->name] = $roleId ? $role->hasPermissionTo($perms->name) : false;
            }

            $thisPermissions = $permission;
            $data = self::toObject([
                'permission' => $thisPermission,
                'permissions' => $thisPermissions,
            ]);
            Cache::forever($hash, $data);
        }

        return $data;
    }

    public function getModulePermissions($id = null, $force = false)
    {
        $hash = md5('getModulePermissions_'.$id);
        if ($force) {
            Cache::forget($hash);
        }

        $data = Cache::get($hash);
        if (empty($data)) {
            $thisPermission = null;
            if ($id) {
                $role = Role::where('id', $id)->first();
            }

            $permissions = Permission::where('name', 'like', 'module.%')->orderBy('name', 'asc')->get();
            foreach ($permissions as $perms) {
                $name = explode('-', $perms->name);
                $permission[$name[0]][$name[1]] = $id ? $role->hasPermissionTo($perms->name) : false;
                $thisPermission[$perms->name] = $id ? $role->hasPermissionTo($perms->name) : false;
            }

            $thisPermissions = $permission;
            $data = self::toObject([
                'permission' => $thisPermission,
                'permissions' => $thisPermissions,
            ]);
            Cache::forever($hash, $data);
        }

        return $data;
    }

    /**
     * @return mixed[]
     */
    public function normalizeRoleName($role_name): ?array
    {
        $role = [];
        if (is_array($role_name)) {
            foreach ($role_name as $name) {
                $role[] = is_array($name) ? $name['name'] : $name;
            }
        }

        return $role;
    }

    public function syncPermissions($role, $permission, $detach = true)
    {
        if ($detach) {
            $role->permissions()->detach();
        }

        $permissions = Permission::whereIn('name', $permission)->get()->pluck('id');
        $data = [];
        foreach ($permissions as $v) {
            $data[] = [
                'role_id' => $role->id,
                'permission_id' => $v,
            ];
        }

        $role->hasPermissions()->upsert($data, ['role_id', 'permission_id']);
        $role->forgetCachedPermissions();

        return $role;
    }

    public function hideRoot($model)
    {
        $level = FacadesAuth::user()?->role_level;
        if (is_null($level)) {
            $level = 999999;
        }

        if ($level) {
            $model->where(function ($q) use ($level) {
                $q->whereHas('roles', function ($q) use ($level) {
                    $q->where('level', '>=', $level);
                })->orWhere('id', FacadesAuth::id());
            });
        }

        if (! FacadesAuth::user() || ! FacadesAuth::user()?->hasRole('Root')) {
            $model->whereDoesntHave('roles', function ($q) {
                $q->where('name', 'Root');
            });
        }

        return $model;
    }

    public function hideRootOnBelongsTo($model)
    {
        $level = FacadesAuth::user()?->role_level ?? 2;
        if ($level) {
            $model->where(function ($q) use ($level) {
                $q->whereHas('roles', function ($q) use ($level) {
                    $q->where('level', '>=', $level);
                })->orWhere('id', FacadesAuth::id());
            });
        }

        if (! FacadesAuth::user()->hasRole('Root')) {
            $model->whereDoesntHave('roles', function ($q) {
                $q->where('name', 'Root');
            });
        }

        return $model;
    }
}
