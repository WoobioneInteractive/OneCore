<?php

/**
 * Helper functions for OneCore
 */
class OnePHP
{
	const PHPFileExtension = '.php';

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

	/**
	 * Check if $haystack begins with $needle
	 * @param string $haystack
	 * @param string $needle
	 * @return bool
	 */
	public static function StringBeginsWith($haystack, $needle)
	{
		$length = strlen($needle);
		return (substr($haystack, 0, $length) === $needle);
	}

	/**
	 * Check if $haystack ends with $needle
	 * @param string $haystack
	 * @param string $needle
	 * @return bool
	 */
	public static function StringEndsWith($haystack, $needle)
	{
		$length = strlen($needle);
		if ($length == 0)
			return true;

		return (substr($haystack, -$length) === $needle);
	}

}