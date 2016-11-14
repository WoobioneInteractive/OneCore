<?php

/**
 * Class ApplicationLoader
 * @uses IApplication
 */
class ApplicationLoader
{
	// Internal constants
	const ApplicationConfigurationFileSuffix = '.Config.php';
	const ApplicationFileSuffix = '.php';

	/**
	 * @var DependencyInjector
	 */
	private $di = null;

	/**
	 * @var IConfigHandler
	 */
	private $configHandler = null;

	/**
	 * @var IPluginLoader
	 */
	private $pluginLoader = null;

	/**
	 * @var string
	 */
	private $mainApplicationIdentifier = null;

	/**
	 * ApplicationLoader constructor.
	 * @param DependencyInjector $di
	 * @param IConfigHandler $configHandler
	 * @param IPluginLoader $pluginLoader
	 */
	public function __construct(DependencyInjector $di, IConfigHandler $configHandler, IPluginLoader $pluginLoader)
	{
		$this->di = $di;
		$this->configHandler = $configHandler;
		$this->pluginLoader = $pluginLoader;
	}

	/**
	 * @param string $applicationName
	 * @return string Application name
	 * @throws ApplicationLoaderException
	 */
	private function validateApplicationName($applicationName) {
		if (is_null($applicationName)) {
			if (is_null($this->mainApplicationIdentifier))
				throw new ApplicationLoaderException('No main application configured');

			$applicationName = $this->mainApplicationIdentifier;
		}

		if (!is_dir($applicationName))
			throw new ApplicationLoaderException("Failed to load application '$applicationName' - no such folder");

		return $applicationName;
	}

	/**
	 * Set main application
	 * @param string $applicationIdentifier
	 */
	public function SetMainApplication($applicationIdentifier)
	{
		$this->mainApplicationIdentifier = $applicationIdentifier;
	}

	public function Load($applicationName = null)
	{
		$applicationName = $this->validateApplicationName($applicationName);
		$applicationDirectory = $applicationName . DIRECTORY_SEPARATOR;

		// Load application main file
		$applicationMainFile = $applicationDirectory . $applicationName . self::ApplicationFileSuffix;
		require_once $applicationMainFile;

		// Load configuration
		$applicationConfigurationFile = $applicationDirectory . $applicationName . self::ApplicationConfigurationFileSuffix;
		$this->configHandler->AddConfigurationFromFile($applicationConfigurationFile);

		return $this->di->AutoWire($applicationName);
	}

	public function IsLoaded()
	{
		return false;
	}

	public function Run()
	{

	}
}

class ApplicationLoaderException extends Exception
{
}