<?php

/**
 * Class Request
 * @author A.G. Netterwall <netterwall@gmail.com>
 * @uses IConfigHandler
 */
class Request implements IRequest
{
	// Configuration options
	const Config_RequestParamName = 'request.requestParamName';

	// Internal constants
	const DefaultRequestParamName = 'request';

	/**
	 * @var IConfigHandler
	 */
	private $configHandler = null;

	/**
	 * Request constructor.
	 * @param IConfigHandler $configHandler
	 */
	public function __construct(IConfigHandler $configHandler)
	{
		$this->configHandler = $configHandler;
	}

	/**
	 * @return string
	 */
	private function getRequestParameterName()
	{
		return $this->configHandler->Get(self::Config_RequestParamName, self::DefaultRequestParamName);
	}

	/**
	 * @return string
	 */
	public function GetRequestString()
	{
		return filter_input(INPUT_GET, $this->getRequestParameterName(), FILTER_SANITIZE_URL);
	}
}