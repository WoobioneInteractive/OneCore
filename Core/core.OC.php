<?php

/**
 * Helper functions for OneCore
 */
class OC {

	/**
	 * @param $className string
	 * @param $interfaceName string
	 */
	public static function ClassImplements($className, $interfaceName) {
		return in_array($interfaceName, class_implements($className));
	}

}