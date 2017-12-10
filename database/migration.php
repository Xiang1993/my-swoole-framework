<?php

require "vendor/autoload.php";

use Illuminate\Database\Capsule\Manager as Capsule;

// 设置时区
date_default_timezone_set('PRC');

// 初始化环境变量
$dotenv = new Dotenv\Dotenv(dirname('../'));   
$dotenv->load();

// 配置
Framework\Path::initBasePath(dirname('../'));

// 数据库初始化
Framework\DB::init();

$schema = Capsule::schema();

if (!$schema->hasTable('users')) {
	$schema->create('users', function ($table) {
	    $table->increments('id');
	    $table->string('email')->unique();
	    $table->timestamps();
	});
}

