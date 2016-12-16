<?php

interface IConfigHandler extends IConfiguration
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
}
