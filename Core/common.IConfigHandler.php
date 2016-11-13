<?php

interface IConfigHandler
{
	/**
	 * Add an array of values to the configuration
	 * @param array $configuration
	 */
	public function AddConfiguration(Array $configuration);

	/**
	 * Add configuration from file
	 * @param string $filePath
	 */
	public function AddConfigurationFromFile($filePath);

	/**
	 * Get the configuration value for $configKey
	 * Returns false if key doesn't exist
	 * @param string $configKey
	 * @return mixed
	 */
	public function Get($configKey);
}