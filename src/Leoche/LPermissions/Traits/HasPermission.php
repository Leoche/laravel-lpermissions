<?php 
namespace Leoche\LPermissions\Traits;

/**
 * Class HasRole
 * @package Leoche\LPermissions\Traits
 *
 * @method static Builder|Collection|\Eloquent role($role, $column = null)
 */
use Illuminate\Support\Str;

trait HasPermission
{
    public function permissions()
    {
        return $this->hasMany(config('lpermissions.permission'));
    }
    public function getPermissions()
    {
        return \Cache::remember(
            'lp.getRolePermissionsById_'.$this->id,
            config('lpermissions.cacheMinutes'),
            function () {
                return $this->permissions();
            }
        );
    }
    public function getPermissionsWithRole()
    {
        $perms = $this->getPermissions;
        $allperms = $perms->merge([]);
    }

    public function hasPermission($routePerm, $method = "*")
    {
        $routePerm = $this->clearPath($routePerm);
        if ($this->getRole && $this->hasPermissionWithRole($this->getRole, $routePerm, $method)) {
            return true;
        }
        $users_permissions = $this->getPermissions;
        foreach ($users_permissions as $perm) {
            $route = $perm->route;
            if ($this->isPath($route, $routePerm)) {
                if ($perm->method == "*" || strtolower($method) == strtolower($perm->method)) {
                    return true;
                }
            }
        }
        return false;
    }
    public function hasPermissionWithRole($role, $routePerm, $method)
    {
        $roles_permissions = $role->getPermissions;
        foreach ($roles_permissions as $perm) {
            $route = $this->clearPath($perm->route);
            if ($this->isPath($route, $routePerm)) {
                if ($perm->method == "*" || strtolower($method) == strtolower($perm->method)) {
                    return true;
                }
            }
        }
        if ($role->getParentRole) {
            return $this->hasPermissionWithRole($role->getParentRole, $routePerm);
        }
        return false;
    }
    public function setPermission($route, $method = "*")
    {
        $model = config('lpermissions.permission', 'Leoche\LPermissions\Models\Eloquent\Permission');
        $perm = (new $model);
        $perm->route = $this->clearPath($route);
        $methods = ["*","POST","GET","PUT","DELETE"];
        $perm->method = "*";
        if (in_array(strtoupper($method), $methods)) {
            $perm->method = $method;
        }
        if ($this instanceof \App\User) {
            $perm->user_id = $this->id;
        } else {
            $perm->role_id = $this->id;
        }
        $perm->save();
    }
    public function removePermission($route, $method = "*")
    {
        $model = config('lpermissions.permission', 'Leoche\LPermissions\Models\Eloquent\Permission');
        $permId = $this->parsePermissionId($route, $method);
    	$perm = (new $model)->find($permId);
    	$perm->delete();
    }
    public function removeAllPermissions()
    {
        foreach($this->permissions as $perms) {
            $perms->delete();
        }
    }
    protected function parsePermissionId($perm, $method)
    {
        if (is_string($perm) || is_numeric($perm)) {
            $model = config('lpermissions.permission', 'Leoche\LPermissions\Models\Eloquent\Permission');
            $key = is_numeric($perm) ? 'id' : 'route';
            $foreign_key = ($this instanceof \App\User) ? 'user_id' : 'role_id';
        	$methods = ["*","POST","GET","PUT","DELETE"];
        	if(!in_array($method, $methods)){
                throw new \InvalidArgumentException('Specified method ' . $method . ' does not exists.');
        	}
            $alias = (new $model)->where($key, $this->clearPath($perm))->where("method",$method)->where($foreign_key, $this->id)->first();
            if (! is_object($alias) || ! $alias->exists) {
                throw new \InvalidArgumentException('Specified perm '.$method.' '.$this->clearPath($perm).' does not exists for '.$foreign_key.' '.$this->id);
            }
            $perm = $alias->getKey();
        }
        $model = '\Illuminate\Database\Eloquent\Model';
        if (is_object($perm) && $perm instanceof $model) {
            $perm = $perm->getKey();
        }
        return (int) $perm;
    }
    public function clearPath($path)
    {
        if ($path[0] == "/") {
            $path = substr($path, 1);
        }
        if ($path[strlen($path)-1] == "/") {
            $path = substr($path, 0, strlen($path)-1);
        }
        return $path;
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
}
