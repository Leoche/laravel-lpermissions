<?php 
namespace Leoche\LPermissions;

use Illuminate\Support\ServiceProvider;

class LPermissionsServiceProvider extends ServiceProvider
{
	protected $defer = false;
	public function boot()
	{
		$this->publishMigrations();
	}
	public function register()
	{
		
	}
	public function publishMigrations(){
		$this->loadMigrationsFrom(__DIR__ . '/../../migrations/');
	}
}