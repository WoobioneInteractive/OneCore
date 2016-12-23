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
		return (substr($haystack, 0, strlen($needle)) === $needle);
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

	/**
	 * Replace $needle in beginning of $string with $replaceBeginningWith
	 * @param string $string
	 * @param string $needle
	 * @param string $replaceBeginningWith
	 * @return string
	 */
	public static function StringReplaceBeginning($string, $needle, $replaceBeginningWith = '')
	{
		if (self::StringBeginsWith($string, $needle))
			$string = $replaceBeginningWith . substr($string, strlen($needle));

		return $string;
	}

	/**
	 * Replace $needle in end of $string with $replaceBeginningWith
	 * @param string $string
	 * @param string $needle
	 * @param string $replaceEndWith
	 * @return string
	 */
	public static function StringReplaceEnd($string, $needle, $replaceEndWith = '') {
		$length = strlen($needle);
		if (self::StringEndsWith($string, $needle) && $length)
			$string = substr($string, 0, -$length) . $replaceEndWith;

		return $string;
	}
}