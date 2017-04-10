<?php 
namespace Leoche\LPermissions\Models\Eloquent;

use Illuminate\Database\Eloquent\Model;

use Leoche\LPermissions\Traits\HasPermission;
use Leoche\LPermissions\Traits\HasRoleInherit;

class Role extends Model
{
    use HasRoleInherit, HasPermission;
    protected $fillable = ['name', 'slug'];

    protected $table = 'roles';


    public function users()
    {
        return $this->hasMany(config('auth.providers.users.model', config('auth.model')))->withTimestamps();
    }

    
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = $value;
        $this->attributes['slug'] = str_slug($value);
    }
}
