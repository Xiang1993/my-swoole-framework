<?php

namespace Framework;

use Framework\Path;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Events\Dispatcher;
use Illuminate\Container\Container;

class DB
{
	public static function init()
	{
		// 数据库初始化
		$config = require Path::get('config/database.php');
		self::initDB($config);	
		
	    $capsule = new Capsule;
	    $capsule->addConnection($config);
	    $capsule->setEventDispatcher(new Dispatcher(new Container));
	    $capsule->setAsGlobal();
	    $capsule->bootEloquent();
	}

	private static function initDB($config)
	{
		$db_driver = $config['driver'];
		$db_host = $config['host'];
		$db_name = $config['database'];
		$db_username = $config['username'];
		$db_password = $config['password'];

		$dbh = new \PDO("{$db_driver}:host={$db_host}", $db_username, $db_password);
		$dbh->exec("CREATE DATABASE `{$db_name}`;
            CREATE USER '{$db_username}'@'{$db_host}' IDENTIFIED BY '{$db_password}';
            GRANT ALL ON `{$db_name}`.* TO '{$db_username}'@'{$db_host}';
            FLUSH PRIVILEGES;");
	}
}