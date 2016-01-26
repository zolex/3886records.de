<?php

class Registry
{
	protected static $items = array();

	protected function __construct() {}

	protected function __clone() {}

	public static function set($key, $value) {

		static::$items[(string)$key] = $value;
	}

	public static function get($key) {

		if (isset(static::$items[(string)$key])) {

			return static::$items[(string)$key];
			
		} else {

			return null;
		}
	}
}