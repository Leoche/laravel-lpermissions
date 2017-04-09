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
        return ($slug == "*" || $this->getRole->slug == $slug);
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
        if ($this->hasPermissionWithRole($this->getRole, $routePerm)) {
            return true;
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
    public function hasPermissionWithRole($role, $routePerm)
    {
        $roles_permissions = $role->getPermissions;
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
        if ($role->parent_role) {
            return $this->hasPermissionWithRole($role->parent_role, $routePerm);
        }
        return false;
    }
    public function isPath($pattern, $path)
    {
        $value = rawurldecode($path);
        if ($pattern == $value) {
            return true;
        }
        $pattern = preg_quote($pattern, '#');
        $pattern = str_replace('\*', '(.*?)', $pattern);
        $pattern = str_replace('\:number', '(\d*?)', $pattern);
        $pattern = str_replace('\:alpha', '([A-z]*?)', $pattern);
        $pattern = str_replace('\:alphanum', '([A-z0-9]*?)', $pattern);
        $pattern = str_replace('\:slug', '([A-z0-9\-\_]*?)', $pattern);

        return (bool) preg_match('#^'.$pattern.'\z#u', $value);
    }
    protected function parseRoleId($role)
    {
        if (is_string($role) || is_numeric($role)) {
            $model = config('lpermissions.role', 'Leoche\LPermissions\Models\Eloquent\Role');
            $key = is_numeric($role) ? 'id' : 'slug';
            $alias = (new $model)->where($key, str_slug($role))->first();
            if (! is_object($alias) || ! $alias->exists) {
                throw new \InvalidArgumentException('Specified role ' . $key . ' does not exists.');
            }
            $role = $alias->getKey();
        }
        $model = '\Illuminate\Database\Eloquent\Model';
        if (is_object($role) && $role instanceof $model) {
            $role = $role->getKey();
        }
        return (int) $role;
    }
    public function setRole($role)
    {
        $roleId = $this->parseRoleId($role);
        $this->role()->associate($roleId);
        $this->save();
        return true;
    }
    public function removeRole()
    {
        $this->role()->dissociate();
        $this->save();
        return true;
    }
}
