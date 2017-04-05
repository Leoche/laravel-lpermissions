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
}