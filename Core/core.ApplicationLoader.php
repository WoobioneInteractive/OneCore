<?php

/**
 * Class ApplicationLoader
 */
class ApplicationLoader
{
	// Internal constants
	const ApplicationConfigurationFileSuffix = '.Config.php';
	const ApplicationFileSuffix = '.php';

	/**
	 * @var IDependencyContainer
	 */
	private $di;

	/**
	 * @var IConfigHandler
	 */
	private $configHandler;

	/**
	 * @var IPluginLoader
	 */
	private $pluginLoader;

	/**
	 * @var IApplicationFactory
	 */
	private $applicationFactory;

	/**
	 * @var ApplicationContext[]
	 */
	private $loadedApplications = [];

	/**
	 * ApplicationLoader constructor.
	 * @param IDependencyContainer $di
	 * @param IConfigHandler $configHandler
	 * @param IPluginLoader $pluginLoader
	 */
	public function __construct(IDependencyContainer $di, IConfigHandler $configHandler, IPluginLoader $pluginLoader)
	{
		$this->di = $di;
		$this->configHandler = $configHandler;
		$this->pluginLoader = $pluginLoader;
		$this->applicationFactory = new AutowiredApplicationFactory($this->di);
	}

	/**
	 * Load application and all its related files
	 * @param string $applicationName
	 * @return ApplicationContext
	 * @throws ApplicationLoaderException
	 */
	public function Load($applicationName)
	{
		if (!$this->IsLoaded($applicationName)) {
			// Validate folder
			$applicationDirectory = $applicationName . DIRECTORY_SEPARATOR;
			if (!is_dir($applicationName))
				throw new ApplicationLoaderException("Could not load application '$applicationName'. There is no folder named '$applicationDirectory'.");

			// Load application main file
			$applicationMainFile = $applicationDirectory . $applicationName . self::ApplicationFileSuffix;
			if (!file_exists($applicationMainFile))
				throw new ApplicationLoaderException("Could not load application '$applicationName'. No application main file exists '$applicationMainFile'.");
			require_once $applicationMainFile;

			// Create application context
			$this->loadedApplications[$applicationName] = new ApplicationContext($applicationName, $applicationDirectory, $this->applicationFactory);

			// Configure plugins
			$this->pluginLoader->SetApplicationDirectory($applicationDirectory);

			// Load configuration
			$applicationConfigurationFile = $applicationDirectory . $applicationName . self::ApplicationConfigurationFileSuffix;
			$this->configHandler->AddConfigurationFromFile($applicationConfigurationFile);

			// Load plugins
			$this->pluginLoader->LoadAll();
		}

		return $this->loadedApplications[$applicationName];
	}

	/**
	 * See if application is loaded
	 * @param string $applicationName
	 * @return bool
	 */
	public function IsLoaded($applicationName)
	{
		return array_key_exists($applicationName, $this->loadedApplications);
	}
}

class ApplicationLoaderException extends Exception
{
}