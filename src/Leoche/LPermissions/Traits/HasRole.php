<?php 
namespace Leoche\LPermissions\Traits;
/**
 * Class HasRole
 * @package Leoche\LPermissions\Traits
 *
 * @method static Builder|Collection|\Eloquent role($role, $column = null)
 */
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
			function ()
			{
				return $this->role();
			}
		);
		return $this->role();
	}
	public function hasRole($slug)
    {
        return $this->getRole()->slug == $slug;
    }
    public function hasPermission($request)
    {
    	$routePerm = $request->path();
    	$roles_permissions = $this->getRole->getPermissions;
    	foreach ($roles_permissions as $perm) 
    	{
    		$route = $perm->route;
    		if($route[0] == "/"){
    			$route = substr($route,1);
    		}
    		if($request->is($route))
    		{
    			if($perm->method == "*" || $request->isMethod($perm->method))
    			{
    			return true;
    			}
    		}
    	}
    	return false;
    }
}