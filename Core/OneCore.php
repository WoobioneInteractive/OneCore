<?php

define('ONECORE_PATH', dirname(__FILE__) . DIRECTORY_SEPARATOR);

/**
 * This is the core itself. This is where the magic happens
 * Author: Anton Netterwall <netterwall@gmail.com>
 */
class OneCore
{

	// Configuration options
	const Config_Debug = 'core.debug';
	const Config_ExceptionHandler = 'core.exceptionHandler';
	const Config_ApplicationIdentifier = 'core.applicationIdentifier';

	// Internal constants
	const CoreExceptionType = 'CoreException';
	const ExceptionHandlerInterfaceName = 'IExceptionHandler';
	const ExceptionHandlerMethod = 'HandleException';

	/**
	 * @var OneCore
	 */
	protected static $instance = null;

	/**
	 * @var bool
	 */
	protected $coreLoaded = false;

	/**
	 * @var CoreConfigHandler
	 */
	public $ConfigHandler = null;

	/**
	 * Get singleton instance of OneCore
	 * @return OneCore
	 */
	public static final function Instance()
	{
		if (!static::$instance)
			static::$instance = new static();
		return static::$instance;
	}

	/**
	 * Load everything essential on instantiation
	 */
	private function __construct()
	{
		if (!$this->coreLoaded) {
			$this->registerExceptionHandler();
			$this->registerCoreAutoloader();
			$this->registerConfigHandler();


			$this->coreLoaded = true;
		}
	}

	/**
	 * No cloning a singleton
	 */
	private function __clone()
	{
	}

	/**
	 * Register autloader
	 */
	private function registerCoreAutoloader()
	{
		spl_autoload_register(function ($className) {
			$fileName = ONECORE_PATH . "core.$className.php";
			if (file_exists($fileName))
				require_once $fileName;
		});
	}

	/**
	 * Register a handler for internal exceptions
	 */
	private function registerExceptionHandler()
	{
		set_exception_handler(function (Exception $e) {
			$e = !is_a($e, self::CoreExceptionType) ? $e : new Exception($e->getMessage());
			$customExceptionHandler = $this->ConfigHandler ? $this->ConfigHandler->Get(self::Config_ExceptionHandler) : null;
			$exceptionHandlerValidation = $this->validateExceptionHandler($customExceptionHandler);
			if (!$customExceptionHandler || $exceptionHandlerValidation !== true) {
				$this->handleUnhandledException($e, $exceptionHandlerValidation);
			} else {
				call_user_func_array([
					$customExceptionHandler,
					self::ExceptionHandlerMethod
				], array($e));
			}
		});
	}

	/**
	 * Register the config handler
	 */
	private function registerConfigHandler()
	{
		if (!$this->ConfigHandler)
			$this->ConfigHandler = new ConfigHandler();
	}

	/**
	 * @param $className string
	 * @return mixed bool : true (valid) | string : Reason invalid
	 */
	private function validateExceptionHandler($className)
	{
		if (!class_exists($className))
			return "Selected exception handler '<i>$className</i>' does not exist";

		if (!OC::ClassImplements($className, self::ExceptionHandlerInterfaceName))
			return "Selected exception handler '<i>$className</i>' does not implement interface '<i>" . self::ExceptionHandlerInterfaceName . "</i>'";

		return true;
	}

	/**
	 * Handle exceptions that occur before exception handling is properly configured
	 * @param Exception $e
	 */
	private function handleUnhandledException(Exception $e, $exceptionHandlerValidation = null)
	{
		echo "An exception was thrown but could not be properly handled. That's not good..<br /><br />";

		if (!$exceptionHandlerValidation) {
			echo "<b>Possible causes</b><br />";
			echo "<b>1.</b> The ConfigHandler was not initialized when the exception occurred.<br />";
			echo "<b>2.</b> There was an error loading or parsing the main configuration file (<i>default: config.php</i>).<br />";
			echo "<b>3.</b> You have not properly configured exception handling.";
		} else {
			echo "<b>Reason</b>: $exceptionHandlerValidation.";
		}

		echo "<br /><br />";
		echo "File: '<b>{$e->getFile()}</b>'<br />";
		echo "Line: <b>{$e->getLine()}</b><br /><br />";
		echo $e->getTraceAsString();
		exit();
	}

	/**
	 * Load config file into config handler
	 * @param $configFilePath string
	 */
	public static function LoadConfig($configFilePath)
	{
		self::Instance()->ConfigHandler->LoadConfig($configFilePath);
	}

	/**
	 * Runs project
	 * @param $applicationName string
	 */
	public static function Run($applicationName = null)
	{

		if (!is_dir($applicationName))
			throw new CoreException("No such application '$applicationName'");

		echo $applicationName;

	}

}

/**
 * Internal exceptions
 */
class CoreException extends Exception
{
}