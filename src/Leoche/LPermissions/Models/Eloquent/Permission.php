<?php 
namespace Leoche\LPermissions\Models\Eloquent;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    // use HasPermission;
    /**
     * The attributes that are fillable via mass assignment.
     *
     * @var array
     */
    protected $fillable = ['route', 'method'];
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'permissions';

    public function roles()
    {
        $model = config('lpermissions.role', 'Leoche\LPermissions\Models\Eloquent\Role');
        return $this->belongsTo($model);
    }
    /**
     * Permissions can belong to many users.
     *
     * @return Model
     */
    public function users()
    {
        return $this->belongsTo(config('auth.providers.users.model', config('auth.model')))->withTimestamps();
    }
}
