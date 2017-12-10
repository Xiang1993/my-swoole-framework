<?php

namespace Framework;

class Path
{
	static $base_path;

	public static function initBasePath($path)
	{
		self::$base_path = $path;
	}

	public static function get($path)
	{
		return self::$base_path.'/'.$path;
	}
}