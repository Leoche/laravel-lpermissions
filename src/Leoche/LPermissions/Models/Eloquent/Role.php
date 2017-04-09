<?php 
namespace Leoche\LPermissions\Models\Eloquent;

use Illuminate\Database\Eloquent\Model;

//use Leoche\LPermissions\Traits\HasPermission;

class Role extends Model
{
    protected $fillable = ['name', 'slug'];

    protected $table = 'roles';


    public function users()
    {
        return $this->hasMany(config('auth.providers.users.model', config('auth.model')))->withTimestamps();
    }
    public function parent_role()
    {
        return $this->hasOne(config('lpermissions.role'), "inherit_id");
    }

    public function getPermissions()
    {
        $this_role = \Cache::remember(
            'lp.getPermissionsById_'.$this->id,
            config('lpermissions.cacheMinutes'),
            function () {
                return $this->permissions();
            }
            );
        return $this->permissions();
    }
    public function permissions()
    {
        return $this->hasMany(config('lpermissions.permission'));
    }
    
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = $value;
        $this->attributes['slug'] = str_slug($value);
    }
}
