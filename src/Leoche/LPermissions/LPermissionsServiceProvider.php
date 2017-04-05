<?php 
namespace Leoche\LPermissions;

use Illuminate\Support\ServiceProvider;

class LPermissionsServiceProvider extends ServiceProvider
{
	protected $defer = false;
	public function boot()
	{
		$this->loadMigrationsFrom(__DIR__ . '/../../migrations/');
		$this->mergeConfigFrom(__DIR__.'/../../config/lpermissions.php', 'lpermissions');
	}
	public function register()
	{
		
	}
}