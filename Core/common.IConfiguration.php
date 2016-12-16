<?php

interface IConfiguration
{
	/**
	 * Get the configuration value for $configKey
	 * Returns false if key doesn't exist
	 * @param string $configKey
	 * @param mixed $valueIfNotExists
	 * @return mixed
	 */
	public function Get($configKey, $valueIfNotExists = false);
}
