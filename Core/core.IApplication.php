<?php

interface IApplication
{
	/**
	 * Plugins to load for the application
	 * @return string[]
	 */
	public function Plugins();
}