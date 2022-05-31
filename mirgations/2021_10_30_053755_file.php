<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class File extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->string('file_name')->comment("文件名");
            $table->string('path_name')->comment('文件路径');
            $table->string('file_type')->comment("文件类型");
            $table->string('file_mime')->comment("文件MIME");
            $table->string('file_size')->comment("文件大小");
            $table->string('original_name')->comment("原始文件名称");
            $table->string('md5')->comment("文件MD5");
            $table->string('name')->comment("备注名称")->nullable();
            $table->integer('width')->comment("图片宽度,当为音频/视频时为播放长度(毫秒)")->default(0);
            $table->integer('height')->comment("图片高度")->default(0);
            $table->timestamp('ctime')->comment("文件最后修改时间")->nullable();
            $table->json("thumbnails")->nullable()->comment("缩率图列表");
            $table->nullableMorphs('fileable');
            $table->comment = '系统图片表';
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('files');
    }
}
