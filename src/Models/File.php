<?php namespace DennisLui\ModelPlus\Models;

use DennisLui\ModelPlus\Attach\Resizer;
use Exception;
use Illuminate\Support\Facades\Storage;
use \Carbon\Carbon;
use \getID3;
use \Symfony\Component\HttpFoundation\File\File as FileObj;
use \Symfony\Component\HttpFoundation\File\UploadedFile;

class File extends Model {

	/**
	 * @var string The table associated with the model.
	 */
	protected $table = 'files';

	protected $uploadType = 'file';

	/**
	 * Picture Extension
	 * 
	 * @var array
	 */
	public static $imageExtensions = ['gif', 'png', 'jpg', 'jpeg', 'webp', 'bmp', 'svg'];

	/**
	 * Video Extension
	 * 
	 * @var array
	 */
	public static $videoExtensions = ['m3u8', 'ts', 'webm', 'mp4'];

	/**
	 * Audio Extension
	 * 
	 * @var array
	 */
	public static $audioExtensions = ['weba', 'mp3'];

	/**
	 * Doc Extension
	 * @var array
	 */
	public static $docExtensions = ['docx', 'xlsx', 'doc', 'xls', 'ppt', 'xml', 'txt'];

	/**
	 * Zip Extension
	 * 
	 * @var array
	 */
	public static $zipExtensions = ['apk', 'ipa', 'zip'];

	/**
	 * @var array Mime types
	 */
	public static $autoMimeTypes = [
		'docx' => 'application/msword',
		'xlsx' => 'application/excel',
		'gif' => 'image/gif',
		'png' => 'image/png',
		'jpg' => 'image/jpeg',
		'jpeg' => 'image/jpeg',
		'webp' => 'image/webp',
		'm3u8' => 'application/x-mpegURL',
		'ts' => 'video/MP2T',
		'webm' => 'video/webm',
		'weba' => 'audio/webm',
		'pdf' => 'application/pdf',
		'svg' => 'image/svg+xml',
		'doc' => 'application/vnd.ms-word',
		'xls' => 'application/vnd.ms-excel',
		'ppt' => 'application/vnd.ms-powerpoint',
		'xml' => 'application/xml',
		'bmp' => 'image/x-ms-bmp',
		'mp3' => 'audio/mpeg',
		'mp4' => 'video/mp4',
		'apk' => 'application/vnd.android.package-archive',
		'ipa' => 'application/iphone-package-archive',
		'zip' => 'application/zip',
		'txt' => 'text/plain',
	];

	/**
	 * Attributes type
	 * 
	 * @var array
	 */
	protected $casts = [
		'ctime' => 'datetime:Y-m-d H:i:s',
		'updated_at' => 'datetime:Y-m-d H:i:s',
		'created_at' => 'datetime:Y-m-d H:i:s',
	];

	/**
	 * Array append to return JSON/Array
	 * @var array
	 */
	protected $appends = ['local_path', 'path'];

	/**
	 * fileable morph
	 * @return \DennisLui\ModelPlus\Relations\MorphTo
	 */
	public function fileable() {
		return $this->morphTo();
	}

	/**
	 *	Returns file path
	 * 
	 * @return string
	 */
	protected function getFilePath() {
		$path = [
			$this->getBasicDisk(),
			$this->getFileDisk(),
			$this->file_name . '.' . $this->file_type,
		];
		return implode('/', $path);
	}

	/**
	 * Returns thumbnail file name
	 * 
	 * @return string
	 */
	protected function getThumbPath($width = 90, $height = 90, $mode = []) {
		$path = [
			'thumbnails',
			$this->getFileDisk(),
			md5($this->id . json_encode($mode)) . '_' . $width . '_' . $height . '.' . $this->file_type,
		];
		return implode('/', $path);
	}

	/**
	 *	Returns new file name
	 * 
	 * @return string
	 */
	protected function getFileDisk() {
		$fileName = array_slice(str_split($this->file_name, 3), 0, 6);
		return implode('/', $fileName);
	}

	/**
	 * Returns basic disk
	 * 
	 * @return string
	 */
	protected function getBasicDisk() {
		if ($this->isImage()) {
			return 'images';
		}

		if ($this->isAudio()) {
			return 'audios';
		}

		if ($this->isVideo()) {
			return 'videos';
		}

		if ($this->isDoc()) {
			return 'docs';
		}

		if ($this->isZip()) {
			return 'zips';
		}

		return 'other';
	}

	/**
	 * Returns local path
	 * 
	 * @return string
	 */
	public function getLocalPathAttribute() {
		return $this->getOriginalPath();
	}

	/**
	 * Returns uri
	 * 
	 * @return string
	 */
	public function getPathAttribute() {
		return Storage::url($this->getFilePath());
	}

	/**
	 * Returns thumbnail uri
	 * 
	 * @param  string $path
	 * @return string
	 */
	public function getPublicThumb($path) {
		return Storage::url($path);
	}

	/**
	 * Returns real path
	 * 
	 * @return string
	 */
	protected function getOriginalPath() {
		return storage_path($this->getFilePath());
	}

	/**
	 * Returns FileSize
	 * 
	 * @param  string $path
	 * @param  string $type
	 * @return array [Width/PlayTime,Height,filesize,file_md5]
	 */
	public static function getFileSize($path, $type = 'image') {
		$width = $height = 0;
		$size = filesize($path);
		$md5 = md5_file($path);
		$getID3 = new getID3;
		$metadata = $getID3->analyze($path);
		switch ($type) {
		case 'image':
			if (isset($metadata['png'])) {
				$width = $metadata['png']['IHDR']['width'];
				$height = $metadata['png']['IHDR']['height'];
			}
			break;
		case 'audio':
		case 'video':
			if (isset($metadata['video'])) {
				$width = $metadata['video']['resolution_x'];
				$height = $metadata['video']['resolution_y'];
			}
			if (isset($metadata['playtime_seconds'])) {
				$width = intval($metadata['playtime_seconds'] * 1000);
			}
			break;

		default:
			if (isset($metadata['png'])) {
				$width = $metadata['png']['IHDR']['width'];
				$height = $metadata['png']['IHDR']['height'];
			}
			break;
		}
		return [$width, $height, $size, $md5];

	}

	public function afterCreate() {
		FileHelper::chmod($this->getOriginalPath());
	}

	protected function deleteFile() {
		unlink($this->getOriginalPath());
	}

	/**
	 * Delete real thumbnails
	 * 
	 * @return [type] [description]
	 */
	protected function deleteThumb() {
		foreach ($this->thumbnails as $path) {
			if (file_exists($path)) {
				unlink($path);
			}
		}
	}

	/**
	 * Check on Picture
	 * 
	 * @return boolean
	 */
	public function isImage() {
		return in_array($this->file_type, static::$imageExtensions);
	}

	/**
	 * Check on Video
	 * 
	 * @return boolean
	 */
	public function isVideo() {
		return in_array($this->file_type, static::$videoExtensions);
	}

	/**
	 * Check on Audio
	 * 
	 * @return boolean
	 */
	public function isAudio() {
		return in_array($this->file_type, static::$audioExtensions);
	}

	/**
	 * Check on Doc
	 * @return boolean
	 */
	public function isDoc() {
		return in_array($this->file_type, static::$docExtensions);
	}

	/**
	 * Check on Zip
	 * 
	 * @return boolean
	 */
	public function isZip() {
		return in_array($this->file_type, static::$zipExtensions);
	}

	/**
	 * Delete local's
	 * 
	 * @return void
	 */
	public function afterDelete() {
		try {
			$this->deleteFile();
			$this->deleteThumb();
		} catch (Exception $ex) {
		}
	}

	/**
	 * Generates and returns a thumbnail path.
	 *
	 * @param integer $width
	 * @param integer $height
	 * @param array $options [
	 *                  'mode'      => 'auto',
	 *                  'offset'    => [0, 0],
	 *                  'quality'   => 90,
	 *                  'sharpen'   => 0,
	 *                  'interlace' => false,
	 *                  'extension' => 'auto',
	 *              ]
	 * @return string The URL to the generated thumbnail
	 */
	public function getThumb($width = 90, $height = 90, $options = []) {
		if (!$this->isImage()) {
			return $this->path;
		}

		$width = (int) $width;
		$height = (int) $height;

		$options = $this->getDefaultThumbOptions($options);

		$thumbFile = $this->getThumbPath($width, $height, $options);

		if (!$this->hasFile($thumbFile)) {
			$this->makeThumbLocal($thumbFile, $this->getOriginalPath(), $width, $height, $options);
		}

		return $this->getPublicThumb($thumbFile);
	}

	/**
	 * get the default options for thumb
	 * 
	 * @param  array  $overrideOptions
	 * @return array
	 */
	protected function getDefaultThumbOptions($overrideOptions = []) {
		$defaultOptions = [
			'mode' => 'auto',
			'offset' => [0, 0],
			'quality' => 90,
			'sharpen' => 0,
			'interlace' => false,
			'extension' => 'auto',
		];

		if (!is_array($overrideOptions)) {
			$overrideOptions = ['mode' => $overrideOptions];
		}

		$options = array_merge($defaultOptions, $overrideOptions);

		$options['mode'] = strtolower($options['mode']);

		if (strtolower($options['extension']) == 'auto') {
			$options['extension'] = $this->file_type;
		}

		return $options;
	}

	/**
	 * Make a file for the thumbnail
	 * 
	 * @param  string $thumbFile
	 * @param  string $file
	 * @param  integer $width
	 * @param  integer $height
	 * @param  array $options
	 * @return void
	 */
	protected function makeThumbLocal($thumbFile, $file, $width, $height, $options) {
		if (!$this->hasFile($file)) {
			$this->brokenImage($thumbFile);
		} else {
			try {
				Resizer::open($file)
					->resize($width, $height, $options)
					->save($thumbFile)
				;
			} catch (Exception $ex) {
				$this->brokenImage($thumbFile);
			}
		}

		FileHelper::chmod($thumbPath);
	}

	/**
	 * Check file exists on storage device.
	 * 
	 * @return void
	 */
	protected function hasFile($fileName = null) {
		return $fileName && file_exists($fileName);
	}

	/**
	 * Broken the thumb
	 * 
	 * @param  string $pathName
	 * @return void
	 */
	protected function brokenImage($pathName) {
		Storage::put($pathName, base64_decode('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'));
	}

	/**
	 * Get php upload limit
	 * 
	 * @return integer
	 */
	public static function getMaxFilesize() {
		return round(UploadedFile::getMaxFilesize() / 1024);
	}

	/**
	 * create a File
	 * 
	 * @param  UploadedFile|string $file
	 * @param  string $name
	 * @param  Model  $model
	 * @return Model
	 */
	public static function makeData($file, string $name, Model $model): Model{
		$fileModel = new self;
		if ($file instanceof UploadedFile) {
			$fileModel->uploadType = 'file';
		} else {
			if (strpos($file, 'http') === 0) {
				$fileModel->uploadType = 'path';
			} else {
				$fileModel->uploadType = 'base';
			}
		}
		$fileModel->fileable_id = $model->id;
		$fileModel->fileable_type = get_class($model);
		$self = $fileModel->saveData($file);
		return $self;
	}

	/**
	 * 保存图片并创建
	 * @param  UploadedFile|string $file
	 * @return Model
	 */
	protected function saveData($file) {
		$file = $this->makeTempFile($file, $this->uploadType);
		$this->path_name = $file->getPathname();
		$this->file_mime = $file->getMimeType();
		$this->file_type = $file->guessExtension();
		$this->original_name = $file->getName();
		$this->file_name = explode('.', $this->original_name)[0];
		$newPath = $this->getFilePath();
		list($width, $height, $file_size, $md5) = $this->getFileSize($file->getRealPath(), $type);
		$this->image_height = $height;
		$this->file_size = $file_size;
		$this->md5 = $md5;
		$this->image_width = $width;
		$this->ctime = Carbon::parse($file->getMTime());
		$file->move($newPath);
		return $this->save();
	}

	/**
	 * save a file to temp storage
	 * @param  UploadedFile|string $file
	 * @param  string $type [file|path|base]
	 * @return FileObj
	 */
	protected function makeTempFile($file, $type = 'file'): FileObj {
		if ($type == 'file' && $file instanceof UploadedFile) {
			$tempFileName = $file->getClientOriginalName();
			$path = $file->store('tmp');
		}
		if ($type == 'path') {
			$tempFileName = last(explode('/', $file));
			Storage::move($file, 'tmp/' . $tempFileName);
		}
		if ($type == 'base') {
			list($file_header, $file_data) = explode(',', $file);
			$file_type = str_replace(['data:', ';base64'], '', $file_header);
			if (!$file_extension = array_search(self::$autoMimeTypes, $file_type)) {
				throw new Exception("The file data has been invalid", 1);

			}
			$tempFileName = md5($file_data) . '.' . $file_extension;
			$file_data = base64_decode($file_data);
			Storage::put('tmp/' . $tempFileName, $file_data);
		}
		return new FileObj(storage_path('app/tmp/' . $tempFileName));
	}
}
