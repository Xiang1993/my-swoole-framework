<?php

namespace Framework;

use Monolog\Logger as MonologLogger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;
use Framework\Path;

class Logger
{
	static $logger;

	public static function init()
	{
		self::$logger = new MonologLogger('log');
		$stream = new StreamHandler(Path::get('/logs/debug.log'), MonologLogger::DEBUG);
		$dateFormat = "Y-m-d H:i:s";
		$output = "%datetime%: %message%\n";
		$stream->setFormatter(new LineFormatter($output, $dateFormat));
		self::$logger->pushHandler($stream);
	}

	public static function __callStatic($method, $params)
	{
		return self::$logger->$method(...$params);
	}
}