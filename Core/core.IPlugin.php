<?php

interface IPlugin
{
	/**
	 * @return bool|string Autoload child files | Custom child file pattern
	 */
	//public function Autoload();

	/**
	 * @return string[]|bool Names of plugins the are required by the plugin | bool false
	 */
	//public function Dependencies();

}