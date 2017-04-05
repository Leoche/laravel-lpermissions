<?php 
namespace Leoche\LPermissions;

use Illuminate\Support\ServiceProvider;

class LPermissionsServiceProvider extends ServiceProvider
{
	protected $defer = false;
	public function boot()
	{
		$this->loadMigrationsFrom(__DIR__ . '/../../migrations/');
		$this->publishes([
            __DIR__ . '/../../config/lpermissions.php' => config_path('lpermissions.php'),
        ], 'config');
	}
	public function register()
	{
		$this->mergeConfigFrom(__DIR__.'/../../config/lpermissions.php', 'lpermissions');
	}
}