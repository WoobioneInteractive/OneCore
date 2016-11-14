<?php

/**
 * DependencyMapping reader for ConfigHandler
 * @uses ConfigHandler
 * @uses DependencyInjector
 */
class DependencyMappingFromConfig implements IDependencyMapping
{
	/**
	 * @var IConfigHandler|null
	 */
	private $configHandler = null;

	/**
	 * ConfigHandlerDependencyMapping constructor.
	 * @param IConfigHandler $configHandler
	 */
	public function __construct(IConfigHandler $configHandler)
	{
		$this->configHandler = $configHandler;
	}

	/**
	 * Gets the dependency mapping from OneCore's config
	 * @return array
	 */
	public function GetMap()
	{
		return $this->configHandler->Get(DependencyInjector::Config_Mapping, []);
	}
}