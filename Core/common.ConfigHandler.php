<?php

/**
 * Config handler
 * @version 1.0
 * @uses OC [Helpers for OneCore]
 */
class ConfigHandler implements IConfigHandler
{
	/**
	 * @var array
	 */
	private $configuration = array();

	/**
	 * ConfigHandler constructor.
	 * - can take default configuration
	 * @param array|string|null $defaultConfiguration Config array | Path to config file
	 */
	public function __construct($defaultConfiguration = null)
	{
		if (!is_null($defaultConfiguration)) {
			if (is_array($defaultConfiguration))
				$this->AddConfiguration($defaultConfiguration);
			else
				$this->AddConfigurationFromFile($defaultConfiguration);
		}
	}

	/**
	 * @param $filePath string
	 * @throws ConfigHandlerException
	 */
	private function loadConfigurationFile($filePath)
	{
		if (!file_exists($filePath))
			throw new ConfigHandlerException("Configuration file '$filePath' does not exist");

		$configuration = require_once $filePath;

		if (!is_array($configuration))
			throw new ConfigHandlerException("Error parsing configuration file '$filePath'");

		return $configuration;
	}

	/**
	 * Add an array of values to the configuration
	 * @param array $configuration
	 */
	public function AddConfiguration(Array $configuration)
	{
		$this->configuration = array_replace_recursive($this->configuration, $configuration);
	}

	/**
	 * Add configuration from file
	 * @param string $filePath
	 */
	public function AddConfigurationFromFile($filePath)
	{
		$this->AddConfiguration($this->loadConfigurationFile($filePath));
	}

	/**
	 * Get the configuration value for $configKey
	 * Returns false if key doesn't exist
	 * @param string $configKey
	 * @return mixed
	 */
	public function Get($configKey)
	{
		return OC::ValueIfExists($configKey, $this->configuration, false);
	}
}

/**
 * Class ConfigHandlerException
 */
class ConfigHandlerException extends Exception
{
}