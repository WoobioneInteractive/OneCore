<?php

class AutowiredApplicationFactory implements IApplicationFactory
{
	/**
	 * @var IDependencyContainer
	 */
	private $di;

	/**
	 * AutowiredApplicationFactory constructor.
	 * @param IDependencyContainer $di
	 */
	public function __construct(IDependencyContainer $di)
	{
		$this->di = $di;
	}

	/**
	 * @param string $applicationClassName
	 * @return IApplication
	 */
	public function Initialize($applicationClassName)
	{
		return $this->di->Autowire($applicationClassName);
	}
}