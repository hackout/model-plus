# LA扩展框架

安装方式\
命令行执行
``` composer require dennislui/model-plus ```\

在composer.json插入 \
```

    "repositories": [
        {
            "url": "git@github.com:hackout/model-plus.git",
            "type": "git"
        }
    ],
```

Provider\
在 ```config\app.php```中的```providers```项中插入\
```
    'providers' => [
    	...,
        DennisLui\ModelPlus\ModelPlusServiceProvider::class,

    ],
```

## 相关Relation

增加了一些relationship

### attachOne
一对一附件关系,关联方式```attachOne($name)``` \
示例代码:
```
public function avatar()
{
	return $this->attachOne('avatar');
}
```

### attachMany
一对多附件关系, 关联方式```attachMany($name)```\
示例代码:
```
public function thumbnails()
{
	return $this->attachMany('thumbnail');
}
```
#### attach 附件关系的上传
文件上传依托PHP中的```fileinfo```扩展进行，使用前请务必安装\
通过应用类```DennisLui\ModelPlus\Models\File```中的```makeData```方法进行上传。\
```makeData```方法支持上传类型: ```UploadFile```,```sqlFileInfo```图片地址,```base64_encode``` 转码文件\
对于Base64的文件需要携带头```data:image/png;base64,代码``` \
Request提交的文件必须要是UploadFile容器的\
图片地址为本地完整\
示例代码: \
```
use DennisLui\ModelPlus\Models\File as FileModel;

$user = User::find(1);

$file = $request->file('avatar');

$fileModel = FileModel::makeData($file,'avatar',$user);

```
#### 附件类操作

```fileable``` 关联的模型


```getThumbPath($width = 90, $height = 90, $mode = [])``` 剪切图片缩略图 \
```mode``` 是剪切的参数
- mode: Either exact, portrait, landscape, auto, fit or crop.
- offset: The offset of the crop = [ left, top ]
- sharpen: Sharpen image, from 0 - 100 (default: 0)
- interlace: Interlace image,  Boolean: false (disabled: default), true (enabled)
- quality: Image quality, from 0 - 100 (default: 90)

```local_path``` 本地文件地址\
```path``` URL预览地址\


## 模型请求优化
支持模型请求二级以上的多级关系对象的查询、删除\
示例代码: 

```
<?php namespace App\Models;
class User{
	public function posts()
	{
		return $this->hasMany(Post::class);
	}
}
class Post{
	public function comments()
	{
		return $this->hasMany(Comment::class);
	}
}

$comments = $user->posts()->comments()->get(); //获取comments collection
$comments = $user->posts()->comments(true); //获取comments collection
$comments = $user->posts()->comments()->delete(); //删除comments
```

## artisan 指令

### make:model 模型名

创建模型名及migration

### make:module 模块名

创建模块在```App\Modules```下\
模块包含:
```
Controller
Middleware
Validation
Route
```

#### 模块路由
模块路由自动读取模块Route目录下的路由文件\
并解析路由格式:
```
Route::prefix('模块名')->middleware('api')
    ->group(['路由文件']);
```


#### 辅助函数

```module_class``` 扫描模块\
```scan_alldir``` 扫描目录\
```scan_files``` 扫描文件\
```json_success``` 返回JSON code = 200\
```json_error``` 返回JSON code = 500\
```json_exception``` 返回JSON code = 501\
```json_sign_in``` 返回JSON code = 400\
```json_allow``` 返回JSON code = 401\
```json_not_found``` 返回JSON code = 404\
```file_download``` 返回文件下载\
```file_view``` 返回文件预览


## Model 模型

``` $fillable ``` 可提交字段\
``` $purgeable ``` 提交字段过滤器\
``` $table ``` 关联表明\
``` $hidden ``` 隐藏返回显示\
``` $appends ``` 追加JSON/Array 显示 配合 ``` public function getSimpleAttribute``` 方法使用\
``` $casts ``` JSON/Array 返回类型 详情见 [Attribute Casting](https://laravel.com/docs/9.x/eloquent-mutators#attribute-casting)\
``` public function beforeCreate(){ } ``` 等同于 ```creating```\
``` public function afterCreate(){ } ``` 等同于 ```created```\
``` public function beforeSave(){ } ``` 等同于 ```saving```\
``` public function afterSave(){ } ``` 等同于 ```saved```\
``` public function beforeDelete(){ } ``` 等同于 ```deleting```\
``` public function afterDelete(){ } ``` 等同于 ```deleted```

### Create多级/数组方法

在模型中可使用create进行提交数组创建及```hasOne``` ```hasMany``` 的关系创建


### Config

在.env 中可设置参数 \
```
API_LIMIT = 60 #API请求限制每秒次数

JSONENCODE = 256 #json_encode 编码， 
```

JSON编码表\
|标识 | int |
|:----|----:|
|JSON_HEX_TAG|1|
|JSON_HEX_AMP|2|
|JSON_HEX_APOS|4|
|JSON_HEX_QUOT|8|
|JSON_FORCE_OBJECT|16|
|JSON_NUMERIC_CHECK|32|
|JSON_UNESCAPED_SLASHES|64|
|JSON_PRETTY_PRINT|128|
|JSON_UNESCAPED_UNICODE|256|
|JSON_ERROR_DEPTH|1|
|JSON_ERROR_STATE_MISMATCH|2|
|JSON_ERROR_CTRL_CHAR|3|
|JSON_ERROR_SYNTAX|4|
|JSON_ERROR_UTF8|5|
|JSON_OBJECT_AS_ARRAY|1|
|JSON_BIGINT_AS_STRING|2|