<?php

/**
 *
 */
class PluginLoader implements IPluginLoader
{
	// Configuration options
	const Config_Plugins = 'pluginloader.plugins';
	const Config_PluginDirectory = 'pluginloader.pluginDirectory';
	const Config_AutoloadPlugins = 'pluginloader.autoloadPlugins';

	// Internal constants
	const PluginInterface = 'IPlugin';
	const DefaultPluginDirectory = 'Plugins';
	const PluginFileSuffix = '.php';

	/**
	 * @var IConfigHandler
	 */
	private $configHandler = null;

	/**
	 * @var IDependencyContainer
	 */
	private $di = null;

	/**
	 * @var IFileAutoLoader
	 */
	private $fileAutoLoader = null;

	/**
	 * @var string
	 */
	private $applicationDirectory = null;

	/**
	 * @var array
	 */
	private $installedPlugins = [];

	/**
	 * @var array
	 */
	private $loadedPlugins = [];

	/**
	 * PluginLoader constructor.
	 * @param IConfigHandler $configHandler
	 */
	public function __construct(IConfigHandler $configHandler, IDependencyContainer $di, IFileAutoLoader $fileAutoLoader)
	{
		$this->configHandler = $configHandler;
		$this->di = $di;
		$this->fileAutoLoader = $fileAutoLoader;
	}

	/**
	 * Get plugin directory
	 * @param string|null $pluginName If provided - get directory for specific plugin. Otherwise get main plugin directory
	 * @return string
	 * @throws PluginLoaderException
	 */
	private function getPluginDirectory($pluginName = null)
	{
		if (is_null($this->GetApplicationDirectory()))
			throw new PluginLoaderException('Failed to find plugin directory - no application directory supplied');

		$mainDirectory = $this->GetApplicationDirectory() . $this->configHandler->Get(self::Config_PluginDirectory, self::DefaultPluginDirectory) . DIRECTORY_SEPARATOR;

		return $mainDirectory . ($pluginName ? $pluginName . DIRECTORY_SEPARATOR : '');
	}

	/**
	 * @param string $pluginName
	 * @return string
	 * @throws PluginLoaderException
	 */
	public function getPluginFilePath($pluginName)
	{
		$pluginFilePath = $this->getPluginDirectory($pluginName) . $pluginName . self::PluginFileSuffix;
		if (!file_exists($pluginFilePath))
			throw new PluginLoaderException("Invalid plugin '$pluginName' - folder contains no main file '$pluginName" . self::PluginFileSuffix . "'");

		return $pluginFilePath;
	}

	/**
	 * @return string[] Installed plugin names
	 * @throws PluginLoaderException
	 */
	private function getInstalledPlugins()
	{
		if (empty($this->installedPlugins)) {
			foreach(scandir($this->getPluginDirectory()) as $pluginName) {
				if (in_array($pluginName, array('.', '..')))
					continue;

				$pluginFilePath = $this->getPluginFilePath($pluginName);
				require_once $pluginFilePath;

				if (!class_exists($pluginName))
					throw new PluginLoaderException("Plugin '$pluginName' contains no class with the same name");

				if (!OnePHP::ClassImplements($pluginName, self::PluginInterface))
					throw new PluginLoaderException("Plugin '$pluginName' does not implement interface '" . self::PluginInterface . "'");

				$this->installedPlugins[$pluginName] = $pluginFilePath;
			}
		}

		return $this->installedPlugins;
	}

	/**
	 * @return string[]
	 */
	private function getLoadedPlugins()
	{
		return $this->loadedPlugins;
	}

	/**
	 * @param string $pluginName
	 * @return bool
	 */
	private function pluginIsInstalled($pluginName)
	{
		return array_key_exists($pluginName, $this->getInstalledPlugins());
	}

	/**
	 * @param string $pluginName
	 * @return bool
	 */
	private function pluginIsLoaded($pluginName)
	{
		return array_key_exists($pluginName, $this->getLoadedPlugins());
	}

	/**
	 * @param string $pluginName
	 * @param IPlugin $pluginInstance
	 */
	private function setPluginLoaded($pluginName, IPlugin $pluginInstance)
	{
		$this->loadedPlugins[$pluginName] = $pluginInstance;
	}

	/**
	 * @param string $pluginName
	 * @return IPlugin
	 */
	private function getLoadedPlugin($pluginName)
	{
		return $this->getLoadedPlugins()[$pluginName];
	}

	/**
	 * Set main directory for looking for plugins folder
	 * @param string $applicationDirectory
	 * @throws PluginLoaderException
	 */
	public function SetApplicationDirectory($applicationDirectory)
	{
		if (!is_null($this->applicationDirectory))
			throw new PluginLoaderException("Trying to change plugin application directory in run time - that's not a good idea");

		$this->applicationDirectory = $applicationDirectory;

		// Find all installed plugins in directory
		$this->getInstalledPlugins();
	}

	/**
	 * @return string
	 */
	public function GetApplicationDirectory()
	{
		return $this->applicationDirectory;
	}

	/**
	 * Load all plugins in directory
	 */
	public function LoadAll()
	{
		foreach ($this->getInstalledPlugins() as $name => $filePath) {
			if (in_array($name, $this->configHandler->Get(self::Config_Plugins, [])))
				$this->Load($name);
		}
	}

	/**
	 * @param string $pluginName
	 * @return IPlugin
	 * @throws PluginLoaderException
	 */
	public function Load($pluginName)
	{
		if (!$this->pluginIsInstalled($pluginName))
			throw new PluginLoaderException("No plugin with name '$pluginName' is installed");

		if (!$this->pluginIsLoaded($pluginName)) {
			// Register autoloader for additional files
			$this->fileAutoLoader->AddFromDirectory($this->getPluginDirectory($pluginName), strtolower($pluginName) . '.{class}');

			$pluginInstance = $this->di->Autowire($pluginName);
			$this->di->AddInstance($pluginInstance, $pluginName);
			$this->setPluginLoaded($pluginName, $pluginInstance);
		}

		return $this->getLoadedPlugin($pluginName);
	}

}

/**
 * Class PluginLoaderException
 */
class PluginLoaderException extends Exception
{
}