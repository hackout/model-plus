<?php namespace DennisLui\ModelPlus;

use CreateFilesTable;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use \Illuminate\Support\Facades\Config;
use \Illuminate\Support\Collection;
use DennisLui\ModelPlus\Support\CollectionServer;

class ModelPlusServiceProvider extends ServiceProvider {
	public function boot(): void {
		if ($this->app->runningInConsole()) {
			$this->bootConfig();
			$this->bootMigrations();
			$this->bootDefaultDisk();
			$this->bootRoute();
			$this->bootCommand();
			$this->bootMacro();
		}
	}

	public function register(): void{
		$this->mergeConfigFrom(__DIR__ . '/../config/modelplus.php', 'modelplus');
	}

	protected function bootCommand(): void{
		$this->commands([
			\DennisLui\ModelPlus\Commands\CreateModelCommand::class,
			\DennisLui\ModelPlus\Commands\CreateModuleCommand::class,
		]);
	}

	protected function bootMacro():void
	{
		foreach(CollectionServer::getFunctions() as $macro=>$funcion)
		{
			Collection::macro($macro,function($parameters) use ($function){
				return (new CollectionServer($this))->{$function}($parameters);
			});
		}
	}

	protected function bootDefaultDisk(): void
	{
		if (!is_dir(app_path('Modules'))) {
			mkdir(app_path('Modules'), 0755);
		}
	}

	protected function bootConfig(): void
	{
		$this->publishes([
			__DIR__ . '/../config/modelplus.php' => config_path('modelplus.php'),
		], 'config');
	}

    protected function bootRoute(): void
    {
        $routes = $this->getModules();
        $this->app->router->getRoutes(function () use ($routes) {
            foreach ($routes as $route) {
                Route::prefix($route['name'])->middleware('api')
                    ->namespace($this->namespace)
                    ->group($route['route']);
            }
        });
    }

	protected function bootMigrations(): void {
		foreach ([CreateFilesTable::class] as $i => $migration) {
			if (class_exists($migration)) {
				continue;
			}
			$this->publishes([
				__DIR__ . '/../migrations/0000_00_00_000000_' . Str::snake($migration) . '.php' => database_path(sprintf(
					'migrations/%s_%s.php',
					date('Y_m_d_His', time() + $i),
					Str::snake($migration)
				)),
			], 'migrations');
		}
	}

	protected function getModules() {
		$list = [];
		if (!is_dir(app_path('Modules'))) {
			return $list;
		}

		$dirs = scandir(app_path('Modules'));
		$array = ['.', '..'];
		foreach ($dirs as $dir) {
			if (!in_array($dir, $array)) {
				if (is_dir(app_path('Modules/' . $dir))) {
					foreach (scandir(app_path('Modules/' . $dir . '/Route')) as $file) {
						if (!in_array($file, $array)) {
							$list[] = ['route' => app_path('Modules/' . $dir . '/Route/' . $file), 'name' => Str::snake($dir, '-')];
						}
					}
				}
			}
		}
		return $list;
	}

	/**
	 * Configure the rate limiters for the application.
	 *
	 * @return void
	 */
	protected function configureRateLimiting(): void{
		RateLimiter::for ('api', function (Request $request) {
			return Limit::perMinute(env("API_LIMIT", 60))->by(optional($request->user())->id ?: $request->ip());
		});
	}
}