<?php

require "vendor/autoload.php";

// 设置时区
date_default_timezone_set('PRC');

// 初始化环境变量
$dotenv = new Dotenv\Dotenv(__DIR__);   
$dotenv->load();

// 配置
Framework\Path::initBasePath(__DIR__);

// 数据库初始化
Framework\DB::init();

// 日志初始化
Framework\Logger::init();           

// 启动http服务
$http = new swoole_http_server(env('host', '0.0.0.0'), env('port', '9501'));

// 请求回调
$http->on('request', function ($swoole_http_request, $swoole_http_response) {
    try {
        Framework\Launcher::dispatch($swoole_http_request, $swoole_http_response);
    } catch (\Exception $e) {
        $swoole_http_response->status(500);
        $swoole_http_response->header('content-type', 'application/json');
        $swoole_http_response->end(json_encode(['error' => $e->getMessage()]));
    }
});

// 开始监听
$http->start();