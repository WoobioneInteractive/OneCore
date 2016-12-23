<?php

interface IPlugin
{
	/**
	 * @return mixed
	 */
	//public function RegisterFiles();

	/**
	 * Register dependencies in the dependency container
	 * @param IDependencyContainer $container
	 */
	//public function RegisterDependencies(IDependencyContainer $container);

	/**
	 * Get plugin's required plugins. Array with plugin names or null for no required plugins
	 * @return string[]|null
	 */
	//public static function RequiredPlugins();

}