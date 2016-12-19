<?php

class ApplicationContext
{
	/**
	 * @var IApplication
	 */
	private $application;

	/**
	 * @var string
	 */
	private $applicationName;

	/**
	 * @var string
	 */
	private $applicationDirectory;

	/**
	 * @var IApplicationFactory
	 */
	private $applicationFactory;

	/**
	 * ApplicationContext constructor.
	 * @param $applicationName
	 * @param IApplicationFactory $applicationFactory
	 */
	public function __construct($applicationName, $applicationDirectory, IApplicationFactory $applicationFactory)
	{
		$this->applicationName = $applicationName;
		$this->applicationDirectory = $applicationDirectory;
		$this->applicationFactory = $applicationFactory;

		$this->validateApplication();
	}

	/**
	 * Validate application
	 * @throws ApplicationLoaderException
	 */
	private function validateApplication()
	{
		if (!class_exists($this->applicationName))
			throw new ApplicationLoaderException("Failed to create ApplicationContext for application '{$this->applicationName}' - no such class '{$this->applicationName}'");

		if (!OnePHP::ClassImplements($this->applicationName, IApplication::class))
			throw new ApplicationLoaderException("Failed to create ApplicationContext for application '{$this->applicationName}' - application does not implement required '" . IApplication::class . "'");
	}

	/**
	 * See if context is executed
	 * @return bool
	 */
	public function IsExecuted()
	{
		return !!$this->application;
	}

	/**
	 * Execute application
	 */
	public function Execute()
	{
		if (!$this->IsExecuted())
			$this->application = $this->applicationFactory->Initialize($this->applicationName);
	}

}