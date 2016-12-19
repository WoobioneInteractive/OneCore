<?php

interface IApplicationFactory
{
	/**
	 * Get instance of application
	 * @param string $applicationClassName
	 * @return IApplication
	 */
	public function Initialize($applicationClassName);
}