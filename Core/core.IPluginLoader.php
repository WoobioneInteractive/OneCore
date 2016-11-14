<?php

interface IPluginLoader
{
	/**
	 * @param string $applicationDirectory
	 */
	public function SetApplicationDirectory($applicationDirectory);

	/**
	 * Load all plugins in directory
	 */
	public function LoadAll();
}