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

	// Request methods
	const Method_Get = 'GET';
	const Method_Post = 'POST';
	const Method_Put = 'PUT';
	const Method_Patch = 'PATCH';
	const Method_Delete = 'DELETE';
	const Method_Options = 'OPTIONS';

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
		//print_r($_SERVER);
		return filter_input(INPUT_GET, $this->getRequestParameterName(), FILTER_SANITIZE_URL);
	}

	/**
	 * Get request method
	 * @return string i.e. Method_Get
	 */
	public function GetMethod()
	{
		return $_SERVER['REQUEST_METHOD'];
	}

	/**
	 * Get data from request
	 * @param string $key
	 * @param string $from Specify where to get data from i.e. Method_Get
	 * @return string
	 */
	public function Data($key = null, $from = null) {
		$data = ($from == self::Method_Get || $this->GetMethod() == self::Method_Get ? $_GET : $_POST);
		return OnePHP::ValueIfExists($key, $data, $data);
	}
}