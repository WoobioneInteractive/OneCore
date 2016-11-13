<?php

/**
 *
 */
class PluginLoader
{
	/**
	 * @var IConfigHandler|null
	 */
	private $configHandler = null;

	/**
	 * PluginLoader constructor.
	 * @param IConfigHandler $configHandler
	 */
	public function __construct(IConfigHandler $configHandler)
	{
		$this->configHandler = $configHandler;
	}

	public function LoadFromDirectory($directoryName)
	{
		if (is_null($directoryName))
			return;


	}

	public function Load($pluginName)
	{

	}

}