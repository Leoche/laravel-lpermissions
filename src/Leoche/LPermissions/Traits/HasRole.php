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
    use HasPermission;
    public function role()
    {
        $model = config("lpermissions.role", 'Leoche\LPermissions\Models\Eloquent\Role');
        return $this->belongsTo($model);
    }
    public function getRole()
    {
        return \Cache::remember(
            'lp.getRoleById_'.$this->id,
            config('lpermissions.cacheMinutes'),
            function () {
                return $this->role();
            }
        );
    }
    public function hasRole($slug)
    {
        return ($slug == "*" || $this->getRole->slug == $slug);
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
