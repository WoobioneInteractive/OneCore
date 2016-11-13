<?php

define('ONECORE_PATH', dirname(__FILE__) . DIRECTORY_SEPARATOR);
define('ONECORE_VERSION', '1.0-beta');

/**
 * This is the core itself. This is where the magic happens
 * Author: Anton Netterwall <netterwall@gmail.com>
 * @uses ConfigHandler
 * @uses DependencyInjector
 * @uses DependencyMappingFromConfig
 */
class OneCore
{
	// Configuration options
	const Config_Debug = 'onecore.debug';
	const Config_ExceptionHandler = 'onecore.exceptionHandler';
	const Config_ApplicationIdentifier = 'onecore.applicationIdentifier';

	// Internal constants
	const CoreFilePrefix = 'core.';
	const CommonFilePrefix = 'common.';
	const FileSuffix = '.php';
	const DefaultConfigFile = 'OneCore.Config.php';
	const CoreExceptionType = 'CoreException';
	const ExceptionHandlerInterface = 'IExceptionHandler';
	const ExceptionHandlerMethod = 'HandleException';
	const ConfigHandlerInterface = 'IConfigHandler';
	const ApplicationInterface = 'IApplication';

	/**
	 * @var OneCore
	 */
	protected static $instance = null;

	/**
	 * @var bool
	 */
	protected $coreLoaded = false;

	/**
	 * @var ConfigHandler
	 */
	public $ConfigHandler = null;

	/**
	 * @var DependencyInjector
	 */
	public $DependencyInjector = null;

	/**
	 * @var PluginLoader
	 */
	public $PluginLoader = null;

	/**
	 * @var ApplicationLoader
	 */
	public $ApplicationLoader = null;

	/**
	 * @var IApplication
	 */
	public $Application = null;

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
			$this->registerAutoloader();

			// Register the config handler
			$this->ConfigHandler = new ConfigHandler(ONECORE_PATH . self::DefaultConfigFile);

			// Register dependency injector
			$this->DependencyInjector = new DependencyInjector([
				self::ConfigHandlerInterface => [
					DependencyInjector::Mapping_RemoteInstance => $this->ConfigHandler
				]
			]);
			$this->DependencyInjector->AddAutowiredMapping('DependencyMappingFromConfig');

			// Register plugin & application loader
			$this->PluginLoader = $this->DependencyInjector->AutoWire('PluginLoader');
			$this->ApplicationLoader = $this->DependencyInjector->AutoWire('ApplicationLoader');

			// All done!
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
	 * - loads all files in the core directory
	 * - first tries Core files, then Common files
	 */
	private function registerAutoloader()
	{
		spl_autoload_register(function ($className) {
			$coreFileName = ONECORE_PATH . self::CoreFilePrefix . $className . self::FileSuffix;
			$commonFileName = ONECORE_PATH . self::CommonFilePrefix . $className . self::FileSuffix;

			// Core files require OneCore to function
			if (file_exists($coreFileName))
				require_once $coreFileName;

			// Common files can be used stand-alone
			if (file_exists($commonFileName))
				require_once $commonFileName;
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
			if ($exceptionHandlerValidation !== true) {
				$this->handleUnhandledException($e, $exceptionHandlerValidation);
			} else {
				call_user_func_array([
					$customExceptionHandler,
					self::ExceptionHandlerMethod
				], array($e));
			}
		});

		/* CATCH FATAL ERRORS - GOOD FOR LOGGING
		register_shutdown_function(function() {

		});*/
	}

	/**
	 * See if className is a valid exception handler
	 * @param $className string
	 * @return array|bool Array with fail reasons | Boolean true for pass
	 */
	private function validateExceptionHandler($className)
	{
		if (empty($className))
			return [
				'The ConfigHandler was not initialized when the exception occurred',
				'The Exception occurred in the ConfigHandler',
				'There was an error reading or parsing the default configuration (' . self::DefaultConfigFile .  ')',
				'You have failed on your quest to properly configure exception handling'
			];

		if (!class_exists($className))
			return ["Selected exception handler '<i>$className</i>' does not exist"];

		if (!OC::ClassImplements($className, self::ExceptionHandlerInterface))
			return ["Selected exception handler '<i>$className</i>' does not implement interface '<i>" . self::ExceptionHandlerInterface . "</i>'"];

		return true;
	}

	/**
	 * Handle exceptions that occur before exception handling is properly configured
	 * @param Exception $e
	 * @param array $exceptionHandlerValidation
	 */
	private function handleUnhandledException(Exception $e, Array $exceptionHandlerValidation)
	{
		echo "An exception was thrown but could not be properly handled. That's not good..<br /><br />";

		echo "<b>Probable cause</b><br />";
		foreach ($exceptionHandlerValidation as $failNumber => $failReason) {
			$failNumber++;
			echo "<b>$failNumber.</b> $failReason.<br />";
		}

		echo "<br /><br />";
		echo "Message: '<b>{$e->getMessage()}</b>'<br />";
		echo "File: '<i>{$e->getFile()}</i>'<br />";
		echo "Line: <b>{$e->getLine()}</b><br /><br />";
		echo $e->getTraceAsString();
		exit();
	}

	/**
	 * Add configuration from array
	 * @param $configuration array
	 */
	public static function Configure(Array $configuration)
	{
		self::Instance()->ConfigHandler->AddConfiguration($configuration);
	}

	/**
	 * Load config file into config handler
	 * @param $configFilePath string
	 */
	public static function ConfigureFromFile($configFilePath)
	{
		self::Instance()->ConfigHandler->AddConfigurationFromFile($configFilePath);
	}

	/**
	 * @param $configKey string
	 * @return mixed
	 */
	public static function GetConfig($configKey)
	{
		return self::Instance()->ConfigHandler->Get($configKey);
	}

	/**
	 * Instantiate class using dependency injector
	 * @param $className string
	 */
	public static function Autowire($className)
	{
		return self::Instance()->DependencyInjector->AutoWire($className);
	}

	/**
	 * Is debug mode enabled
	 * @return bool
	 */
	public static function IsDebug() {
		return (bool)self::GetConfig(self::Config_Debug);
	}

	/**
	 * Runs application
	 * @param $applicationName string
	 * @throws CoreException
	 */
	public static function Run($applicationName = null)
	{
		// If application name not specified - run configured application
		if (empty($applicationName))
			$applicationName = self::GetConfig(self::Config_ApplicationIdentifier);

		if (!is_dir($applicationName))
			throw new CoreException("No such application '$applicationName'");

		self::Instance()->Application = self::Instance()->ApplicationLoader->Load($applicationName);
		//self::Instance()->Application->Run();
	}
}

/**
 * Internal exceptions
 */
class CoreException extends Exception
{
}