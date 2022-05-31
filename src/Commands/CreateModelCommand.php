<?php

namespace DennisLui\ModelPlus\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class CreateModelCommand extends Command {
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'create:model {model}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Create a Model';

	/**
	 * The type of class being generated.
	 *
	 * @var string
	 */
	protected $model_name;
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
		'model/model.stub' => 'app/Models/{{studly_name}}.php',
		'model/table.stub' => 'database/migrations/{{time}}_create_{{snake_plural_name}}_table.php',
	];

	/**
	 * Prepare variables for stubs.
	 *
	 * return @array
	 */
	protected function prepareVars() {

		return [
			'name' => $this->model_name,
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
			'model_name' => $this->model_name,
			'time' => $this->time,
			'studly_name' => $this->studly_name,
			'snake_name' => $this->snake_name,
			'snake_plural_name' => $this->snake_plural_name,
			'studly_plural_name' => $this->studly_plural_name,
		];
	}

	protected function makeTemplate() {
        $loader = new \Twig\Loader\ArrayLoader($this->stubs);
        $files = [];
		foreach ($this->stubs as $key => $rs) {
			$twig = new \Twig\Environment($loader);
            $file_name = $twig->render($key, $this->getVars());
            $files[$file_name] = file_get_contents(dirname(__DIR__).'/Commands/'.$key);
		}
        if(!$files) return ;
        $loader = new \Twig\Loader\ArrayLoader($files);
        foreach ($files as $key => $rs) {
            $twig = new \Twig\Environment($loader);
            $content = $twig->render($key, $this->getVars());
            file_put_contents(base_path($key),$content);
        }
	}

	/**
	 * Execute the console command.
	 *
	 * @return int
	 */
	public function handle() {
		$this->model_name = $this->argument('model');
		if (!$this->model_name) {
			$this->error("请输入Model");
		}
		$this->snake_name = Str::snake($this->snake_name);
		$this->studly_name = Str::studly($this->model_name);
		$this->studly_plural_name = Str::plural($this->model_name);
		$this->snake_plural_name = Str::snake($this->studly_plural_name);
		$this->makeTemplate();
		$this->info('创建Model成功');
	}

}
