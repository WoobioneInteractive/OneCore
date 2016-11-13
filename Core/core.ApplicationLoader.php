<?php

/**
 * Class ApplicationLoader
 * @uses IApplication
 */
class ApplicationLoader
{
	private $configHandler = null;

	/**
	 * ApplicationLoader constructor.
	 * @param IConfigHandler $configHandler
	 */
	public function __construct(IConfigHandler $configHandler)
	{
		$this->configHandler = $configHandler;
		//$this->configHandler->Get()
	}

	public function Load($applicationName)
	{

	}
}