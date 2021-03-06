<?php 
namespace Leoche\LPermissions\Traits;

/**
 * Class HasRoleInherit
 * @package Leoche\LPermissions\Traits
 *
 * @method static Builder|Collection|\Eloquent role($role, $column = null)
 */
use Illuminate\Support\Str;

trait HasRoleInherit
{
    public function parent_role()
    {
        return $this->belongsTo(config('lpermissions.role'), "inherit_id");
    }
    public function getParentRole()
    {
        return \Cache::remember(
            'lp.getParentRoleById_'.$this->id,
            config('lpermissions.cacheMinutes'),
            function () {
                return $this->parent_role();
            }
        );
    }
    public function child_role()
    {
        return $this->hasMany(config('lpermissions.role'), "inherit_id");
    }
    public function getChildsRole()
    {
        return \Cache::remember(
            'lp.getChildRoleById_'.$this->id,
            config('lpermissions.cacheMinutes'),
            function () {
                return $this->child_role();
            }
        );
    }
    public function setInheritRole($role)
    {
        $roleId = $this->parseRoleId($role);
        if($roleId !== $this->id){
        	$this->inherit_id = $roleId;
        	$this->save();
        	return true;
        }
        return false;
    }
    public function removeInheritRole()
    {
        $this->inherit_id = null;
        $this->save();
        return true;
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
}