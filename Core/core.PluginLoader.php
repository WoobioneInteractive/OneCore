<?php

/**
 *
 */
class PluginLoader implements IPluginLoader
{
	// Configuration options
	const Config_PluginDirectory = 'pluginloader.pluginDirectory';
	const Config_AutoloadPlugins = 'pluginloader.autoloadPlugins';

	// Internal constants
	const DefaultPluginDirectory = 'Plugins';

	/**
	 * @var IConfigHandler
	 */
	private $configHandler = null;

	/**
	 * @var string
	 */
	private $applicationDirectory = null;

	/**
	 * @var array
	 */
	private $loadedPlugins = [];

	/**
	 * PluginLoader constructor.
	 * @param IConfigHandler $configHandler
	 */
	public function __construct(IConfigHandler $configHandler)
	{
		$this->configHandler = $configHandler;
	}

	/**
	 * Get plugin directory
	 * @return string
	 * @throws PluginLoaderException
	 */
	private function getPluginDirectory()
	{
		if (is_null($this->applicationDirectory))
			throw new PluginLoaderException('Failed to find plugin directory - no application directory supplied');

		return $this->applicationDirectory . $this->configHandler->Get(self::Config_PluginDirectory, self::DefaultPluginDirectory);
	}

	/**
	 * Set main directory for looking for plugins folder
	 * @param string $applicationDirectory
	 * @throws PluginLoaderException
	 */
	public function SetApplicationDirectory($applicationDirectory)
	{
		if (!is_null($this->applicationDirectory))
			throw new PluginLoaderException('Trying to change plugin application directory in run time - that is not a good idea');

		$this->applicationDirectory = $applicationDirectory;
	}

	public function LoadAll()
	{
		foreach(scandir($this->getPluginDirectory()) as $directory) {
			if (in_array($directory, array('.', '..')))
				continue;

			var_dump($directory);
		}
	}

	/**
	 * @param string $pluginName
	 */
	public function Load($pluginName)
	{

	}

}

/**
 * Class PluginLoaderException
 */
class PluginLoaderException extends Exception
{
}