<?php

interface IPluginLoader
{
	/**
	 * @param string $applicationDirectory
	 */
	public function SetApplicationDirectory($applicationDirectory);

	/**
	 * @return string
	 */
	public function GetApplicationDirectory();

	/**
	 * Load all plugins in directory
	 */
	public function LoadAll();
}