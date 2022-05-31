<?php

namespace DennisLui\ModelPlus\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class CreateModuleCommand extends Command {
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'create:module {module}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Create a module package';

	/**
	 * The type of class being generated.
	 *
	 * @var string
	 */
	protected $module_name;
	protected $time;
	protected $studly_name;
	protected $snake_name;
	protected $snake_plural_name;
	protected $studly_plural_name;
	/**
	 * A mapping of stub to generated file.
	 *
	 * @var array
	 */
	protected $stubs = [
		'module/controller.stub' => 'Modules/{{studly_name}}/Controller/{{studly_name}}Controller.php',
        'module/middleware.stub' => 'Modules/{{studly_name}}/Middleware/{{studly_name}}Middleware.php',
        'module/route.stub' => 'Modules/{{studly_name}}/Route/route.php',
	];

	/**
	 * Prepare variables for stubs.
	 *
	 * return @array
	 */
	protected function prepareVars() {

		return [
			'name' => $this->module_name,
		];
	}
	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct() {
		parent::__construct();
		$this->time = date('Y_m_d_His');
	}

	protected function getVars() {
		return [
			'module_name' => $this->module_name,
			'time' => $this->time,
			'studly_name' => $this->studly_name,
			'snake_name' => $this->snake_name,
			'snake_plural_name' => $this->snake_plural_name,
			'studly_plural_name' => $this->studly_plural_name,
		];
	}

    protected function getDirVars()
    {
        return [
            app_path('Modules/' . $this->studly_name),
            app_path('Modules/' . $this->studly_name . '/Controller'),
            app_path('Modules/' . $this->studly_name . '/Middleware'),
            app_path('Modules/' . $this->studly_name . '/Validation'),
            app_path('Modules/' . $this->studly_name . '/Route'),
        ];
    }

	protected function makeTemplate() {
        $dirList = $this->getDirVars();
            $stubs = $this->stubs;
        foreach($dirList as $dir)
        {
            if(!is_dir($dir))
                mkdir($dir,0775);
        }
		$loader = new \Twig\Loader\ArrayLoader($stubs);
		$files = [];
		foreach ($stubs as $key => $rs) {
			$twig = new \Twig\Environment($loader);
			$file_name = $twig->render($key, $this->getVars());
			$files[$file_name] = file_get_contents(dirname(__DIR__).'/Commands/' . $key);
		}
		if (!$files) {
			return;
		}

		$loader = new \Twig\Loader\ArrayLoader($files);
		foreach ($files as $key => $rs) {
			$twig = new \Twig\Environment($loader);
			$content = $twig->render($key, $this->getVars());
			file_put_contents(app_path($key), $content);
		}
	}

	/**
	 * Execute the console command.
	 *
	 * @return int
	 */
	public function handle() {
		$this->module_name = $this->argument('module');
		if (!$this->module_name) {
			return $this->error("请输入Module");
		}
		if (!is_dir(app_path('Modules'))) {
			mkdir(app_path('Modules'), 0775);
		}
        $this->snake_name = Str::snake($this->module_name);
        $this->studly_name = Str::studly($this->module_name);
        $this->studly_plural_name = Str::plural($this->module_name);
        $this->snake_plural_name = Str::snake($this->studly_plural_name);
		if (is_dir(app_path('Modules/' . $this->studly_plural_name))) {
			return $this->error("Module 目录已存在");
		}
		$this->makeTemplate();
		$this->info('创建Module成功');
	}
}
