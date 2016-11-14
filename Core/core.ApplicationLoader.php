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
	const ApplicationInterface = 'IApplication';

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
		if (is_null($this->mainApplicationIdentifier))
			throw new ApplicationLoaderException("No main application configured when trying to run application '$applicationName'");

		$applicationName = $applicationName ?: $this->mainApplicationIdentifier;

		if (!is_dir($applicationName))
			throw new ApplicationLoaderException("Failed to load application '$applicationName' - no such folder");

		return $applicationName;
	}

	/**
	 * Set main application
	 * @param string $applicationIdentifier
	 * @throws ApplicationLoaderException
	 */
	public function SetMainApplication($applicationIdentifier)
	{
		if (!is_null($this->mainApplicationIdentifier))
			throw new ApplicationLoaderException('Trying to set main application multiple times');

		$this->mainApplicationIdentifier = $applicationIdentifier;
	}

	/**
	 * Load application and all its related files
	 * @param string|null $applicationName | use main application
	 * @return string
	 * @throws ApplicationLoaderException
	 */
	public function Load($applicationName = null)
	{
		$applicationName = $this->validateApplicationName($applicationName);
		$applicationDirectory = $applicationName . DIRECTORY_SEPARATOR;

		// Load application main file
		$applicationMainFile = $applicationDirectory . $applicationName . self::ApplicationFileSuffix;
		require_once $applicationMainFile;

		// Validate application
		if (!OnePHP::ClassImplements($applicationName, self::ApplicationInterface))
			throw new ApplicationLoaderException("Invalid application found '$applicationName' - application does not implement required '" . self::ApplicationInterface . "'");

		// Load configuration
		$applicationConfigurationFile = $applicationDirectory . $applicationName . self::ApplicationConfigurationFileSuffix;
		$this->configHandler->AddConfigurationFromFile($applicationConfigurationFile);

		$this->pluginLoader->SetApplicationDirectory($applicationDirectory);
		$this->pluginLoader->LoadAll();

		return $applicationName;
	}

	public function IsLoaded()
	{
		return false;
	}

	public function Run($applicationName = null)
	{
		$applicationName = $this->Load($applicationName);

		$this->di->AutoWire($applicationName);
	}
}

class ApplicationLoaderException extends Exception
{
}