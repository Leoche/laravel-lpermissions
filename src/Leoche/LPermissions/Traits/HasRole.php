<?php 
namespace Leoche\LPermissions\Traits;

/**
 * Class HasRole
 * @package Leoche\LPermissions\Traits
 *
 * @method static Builder|Collection|\Eloquent role($role, $column = null)
 */
use Illuminate\Support\Str;

trait HasRole
{
    public function role()
    {
        $model = config("lpermissions.role", 'Leoche\LPermissions\Models\Eloquent\Role');
        return $this->belongsTo($model);
    }
    public function getRole()
    {
        $this_role = \Cache::remember(
            'lp.getRoleById_'.$this->id,
            config('lpermissions.cacheMinutes'),
            function () {
                return $this->role();
            }
        );
        return $this->role();
    }
    public function hasRole($slug)
    {
        return $this->getRole->slug == $slug;
    }

    public function permissions()
    {
        return $this->hasMany(config('lpermissions.permission'));
    }
    public function getPermissions()
    {
        $this_role = \Cache::remember(
            'lp.getRolePermissionsById_'.$this->id,
            config('lpermissions.cacheMinutes'),
            function () {
                return $this->permissions();
            }
        );
        return $this->permissions();
    }
    public function hasPermission($routePerm, $method = "*")
    {
        if ($routePerm[0] == "/") {
            $routePerm = substr($routePerm, 1);
        }
        if ($this->getRole) {
            $roles_permissions = $this->getRole->getPermissions;
            foreach ($roles_permissions as $perm) {
                $route = $perm->route;
                if ($route[0] == "/") {
                    $route = substr($route, 1);
                }
                if ($this->isPath($route, $routePerm)) {
                    if ($perm->method == "*" || strtolower($method) == strtolower($perm->method)) {
                        return true;
                    }
                }
            }
        }
        $users_permissions = $this->getPermissions;
        foreach ($users_permissions as $perm) {
            $route = $perm->route;
            if ($route[0] == "/") {
                $route = substr($route, 1);
            }
            if ($this->isPath($route, $routePerm)) {
                if ($perm->method == "*" || strtolower($method) == strtolower($perm->method)) {
                    return true;
                }
            }
        }
        return false;
    }
    public function isPath($pattern, $path)
    {
        return Str::is($pattern, rawurldecode($path));
    }
}
