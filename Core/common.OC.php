<?php

/**
 * Helper functions for OneCore
 */
class OC
{

	/**
	 * @param string $key
	 * @param array $array
	 * @param mixed $default
	 * @return mixed|null
	 */
	public static function ValueIfExists($key, Array $array, $default = null) {
		return array_key_exists($key, $array) ? $array[$key] : $default;
	}

	/**
	 * See if class implements interface
	 * @param string|object $class Class name | Object instance
	 * @param string $interfaceName
	 * @return bool
	 */
	public static function ClassImplements($class, $interfaceName)
	{
		return is_string($class) ? in_array($interfaceName, class_implements($class)) : is_a($class, $interfaceName);
	}

}