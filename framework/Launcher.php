<?php

namespace Framework;

use Framework\Http\Request;
use Framework\Http\Response;
use Framework\Logger;
use Framework\Path;

class Launcher
{
	/**
	 * 路由分发
	 */
	public static function dispatch($swoole_http_request, $swoole_http_response)
	{
		// 格式化请求
	    $request = new Request($swoole_http_request);

	    // 格式化响应
	    $response = new Response($swoole_http_response);

	    // 记录请求日志
	    Logger::error($request->method().' '.$request->fullUrl());
	    Logger::error(json_encode($request->all(), JSON_UNESCAPED_UNICODE));

	    //  注册路由
	    $dispatcher = \FastRoute\simpleDispatcher(function(\FastRoute\RouteCollector $router) {
	    	require Path::get('router/api.php');
	    });

	    // 路由分发
	    $msg = $dispatcher->dispatch($request->method(), $request->uri());
	    switch ($msg[0]) {
	    	case \FastRoute\Dispatcher::NOT_FOUND:
	    		$response->json(['error' => 'resource not found'])->withStatus(404)->send();
	    		break;
	    	case \FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
	    		$response->json(['error' => 'method not allowed'])->withStatus(405)->send();
	    		break;
	    	case \FastRoute\Dispatcher::FOUND:
	    		self::handleRouter($msg[1], $request, $response, $msg[2]);
				break;
	    	default:
	    		$response->json(['error' => '内部错误'])->withStatus(500)->send();
	    		break;
	    }

	    // 日志换行
	    Logger::error("\n\n");
	}

	/**
	 * 处理路由
	 */
	private static function handleRouter($handler, $request, $response, $vars)
	{
		if (is_callable($handler)) {
			self::handleCallback($handler, $request, $response, $vars);
		} else {
			self::handleController($handler, $request, $response, $vars);
		}
	}

	/**
	 * 回调函数
	 */
	private static function handleCallback($handler, $request, $response, $vars)
	{
		call_user_func_array($handler, array_merge([$request, $response], array_values($vars)));
	}

	/**
	 * 控制器
	 */
	private static function handleController($handler, $request, $response, $vars)
	{
		// 解析类和方法
        $arr = explode('@', $handler);
        if (count($arr) != 2) {
            $response->json(['error' => "handler $handler not found"])->withStatus(500)->send();
        }

        // 判断控制器类是否存在
        $controller = 'App\Controllers\\'.$arr[0];
        if (!class_exists($controller)) {
            $response->json(['error' => "class {$controller} not found"])->withStatus(500)->send();
        }

        $object = new $controller;
        $method = $arr[1];

        // 判断控制器对象中的方法是否存在
        if (!method_exists($object, $method)) {
            $response->json(['error' => "method {$method} not found in class {$controller}"], 500)->send();
        }

        // 执行类对象中的方法
        try {
            call_user_func_array(array($object, $method), array_merge([$request, $response], array_values($vars)));
        } catch (\Exception $e) {
            $response->json(['error' => $e->getMessage()])->withStatus(500)->send();
        }
	}
}