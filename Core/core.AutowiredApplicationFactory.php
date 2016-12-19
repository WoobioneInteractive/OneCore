<?php

class AutowiredApplicationFactory implements IApplicationFactory
{
	/**
	 * @var DependencyInjector
	 */
	private $di;

	/**
	 * AutowiredApplicationFactory constructor.
	 * @param DependencyInjector $di
	 */
	public function __construct(DependencyInjector $di)
	{
		$this->di = $di;
	}

	/**
	 * @param string $applicationClassName
	 * @return IApplication
	 */
	public function Initialize($applicationClassName)
	{
		return $this->di->AutoWire($applicationClassName);
	}
}