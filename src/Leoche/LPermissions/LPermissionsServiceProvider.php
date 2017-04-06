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
        Blade::directive('role', function ($expression) {
            return "<?php if (Auth::check() && Auth::user()->hasRole({$expression})): ?>";
        });
        Blade::directive('endrole', function () {
            return "<?php endif; ?>";
        });
        // permission
        Blade::directive('permission', function ($expression) {
            return "<?php if (Auth::check() && Auth::user()->hasPermision({$expression})): ?>";
        });
        Blade::directive('endpermission', function () {
            return "<?php endif; ?>";
        });
	}
	public function register()
	{
		$this->mergeConfigFrom(__DIR__.'/../../config/lpermissions.php', 'lpermissions');
	}
}