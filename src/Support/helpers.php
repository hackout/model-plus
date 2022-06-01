<?php

/**
 * Builder a array_is_list function for php less then 8.0 version.
 * 
 * @var array $array
 * @return boolean
 */
if (!function_exists('array_is_list')) {
	function array_is_list($array = []) {
		if(!$array) return true;
		$vars = implode('.',array_keys($array));
		$arr = [];
		for($i=1;$i <= count($array);$i++)
		{
			$arr[] = $i;
		}
		return implode('.',$arr) == $vars;
	}
}

/**
 * Creating a new modules class instance
 * 
 * @var string $module_name
 * @return array
 */
if (!function_exists('module_class')) {
	function module_class($module_name = null) {
		$list = [];
		$dirs = scan_files('Modules');
		foreach ($dirs as $key => $rs) {
			if ($module_name && strpos($key, 'Modules/' . $module_name) === false) {
				continue;
			}

			if (strpos($key, '.php') !== false) {
				$file = str_replace(['.php', '.'], '', $key);
				$class_name = '\\App\\' . str_replace('/', '\\', $file);
				$list[] = $class_name;
			}
		}
		return $list;
	}
}

/**
 * Use scandir for disk
 * 
 * @var array $path
 * @return array
 */
if (!function_exists('scan_alldir')) {
	function scan_alldir($path = null) {
		$list = [];
		if (!$path || !is_dir(app_path($path))) {
			return $list;
		}

		$array = ['.', '..'];
		$dirs = scandir(app_path($path));
		foreach ($dirs as $dir) {
			if (!in_array($dir, $array) && is_dir(app_path($path . '/' . $dir))) {
				$list[$path . '/' . $dir] = app_path($path . '/' . $dir);
				$children = scan_alldir($path . '/' . $dir);
				foreach ($children as $key => $rs) {
					$list[$key] = $rs;
				}
			}
		}
		return $list;
	}
}

/**
 * Use scandir for files
 * 
 * @var array $path
 * @return array
 */
if (!function_exists('scan_files')) {
	function scan_files($path = null) {
		$list = [];
		if (!$path || !is_dir(app_path($path))) {
			return $list;
		}
		$array = ['.', '..'];
		$dirs = scandir(app_path($path));
		foreach ($dirs as $dir) {
			if (!in_array($dir, $array)) {
				if (is_file(app_path($path . '/' . $dir))) {
					$list[$path . '/' . $dir] = app_path($path . '/' . $dir);
				}
				if (is_dir(app_path($path . '/' . $dir))) {
					$children = scan_files($path . '/' . $dir);
					foreach ($children as $key => $rs) {
						$list[$key] = $rs;
					}
				}
			}
		}
		return $list;
	}
}

/**
 * The response result is successfully
 * 
 * @var array $data
 * @var string $msg
 * @var string $jsonp
 * @return Response JSON/JSONP
 */
if (!function_exists('json_success')) {
	function json_success($data = [], $msg = 'successfully.', $jsonp = null) {
		$result = array_merge([
			'code' => 200,
			'msg' => $msg,
			'data' => [],
		], $data);
		if ($jsonp) {
			return response()->json($result)->setEncodingOptions(env('JSONENCODE', 0))->withCallback($jsonp);
		}

		return response()->json($result)->setEncodingOptions(env('JSONENCODE', 0));
	}
}

/**
 * The response result is incorrect
 * 
 * @var array $msg
 * @var string $jsonp
 * @return Response JSON/JSONP
 */
if (!function_exists('json_error')) {
	function json_error($msg = 'something is not incorrect.', $jsonp = null) {
		$result = [
			'code' => 500,
			'msg' => $msg,
			'data' => [],
		];
		if ($jsonp) {
			return response()->json($result)->setEncodingOptions(env('JSONENCODE', 0))->withCallback($jsonp);
		}

		return response()->json($result)->setEncodingOptions(env('JSONENCODE', 0));
	}
}

/**
 * The response result is an exception
 * 
 * @var array $msg
 * @var string $jsonp
 * @return Response JSON/JSONP
 */
if (!function_exists('json_exception')) {
	function json_exception($msg = 'The Request is an exception.', $exception = null, $jsonp = null) {
		$result = [
			'code' => 501,
			'msg' => $msg,
			'data' => [],
		];
		if ($exception) {
			logger()->error('[MESSAGE]' . $exception->getMessage() . '[FILE]' . $exception->getFile() . '[LINE]' . $exception->getLine());
		}
		if ($jsonp) {
			return response()->json($result)->setEncodingOptions(env('JSONENCODE', 0))->withCallback($jsonp);
		}

		return response()->json($result)->setEncodingOptions(env('JSONENCODE', 0));
	}
}

/**
 * The response result is guest
 * 
 * @var array $msg
 * @var string $jsonp
 * @return Response JSON/JSONP
 */
if (!function_exists('json_sign_in')) {
	function json_sign_in($msg = 'You need sign in.', $jsonp = null) {
		$result = [
			'code' => 400,
			'msg' => $msg,
			'data' => [],
		];
		if ($jsonp) {
			return response()->json($result)->setEncodingOptions(env('JSONENCODE', 0))->withCallback($jsonp);
		}

		return response()->json($result)->setEncodingOptions(env('JSONENCODE', 0));
	}
}

/**
 * The response result is permission has been failed.
 * 
 * @var array $msg
 * @var string $jsonp
 * @return Response JSON/JSONP
 */
if (!function_exists('json_allow')) {
	function json_allow($msg = 'The response result is permission has been failed.', $jsonp = null) {
		$result = [
			'code' => 401,
			'msg' => $msg,
			'data' => [],
		];
		if ($jsonp) {
			return response()->json($result)->setEncodingOptions(env('JSONENCODE', 0))->withCallback($jsonp);
		}

		return response()->json($result)->setEncodingOptions(env('JSONENCODE', 0));
	}
}

/**
 * The response result is permission has been failed.
 * 
 * @var array $msg
 * @var string $jsonp
 * @return Response JSON/JSONP
 */
if (!function_exists('json_not_found')) {
	function json_not_found($msg = 'The page is not found.', $jsonp = null) {
		$result = [
			'code' => 404,
			'msg' => $msg,
			'data' => [],
		];
		if ($jsonp) {
			return response()->json($result)->setEncodingOptions(env('JSONENCODE', 0))->withCallback($jsonp);
		}

		return response()->json($result)->setEncodingOptions(env('JSONENCODE', 0));
	}
}

/**
 * The response result is download
 * 
 * @var file/path $file
 * @var string $name
 * @var array $headers
 * @return DownloadResponse
 */
if (!function_exists('file_download')) {
	function file_download($file, $name = null, $headers = []) {
		return response()->download($file, $name, $headers);
	}
}

/**
 * The response result is a file
 * 
 * @var file/path $file
 * @var array $headers
 * @return FileResponse
 */
if (!function_exists('file_view')) {
	function file_view($file, $headers = []) {
		return response()->file($file, $headers);
	}
}
