<?php

define('ONECORE_PATH', dirname(__FILE__) . DIRECTORY_SEPARATOR);
define('ONECORE_VERSION', '1.0-beta');

/**
 * This is the core itself. This is where the magic happens
 * Author: Anton Netterwall <netterwall@gmail.com>
 */
class OneCore
{
	// Configuration options
	const Config_Debug = 'onecore.debug';
	const Config_ExceptionHandler = 'onecore.exceptionHandler';
	const Config_MainApplicationIdentifier = 'onecore.mainApplicationIdentifier';
	const Config_Dependencies = 'onecore.dependencies';

	// Internal constants
	const CoreFilePrefix = 'core.';
	const CommonFilePrefix = 'common.';
	const FileSuffix = '.php';
	const DefaultConfigFile = 'OneCore.Config.php';

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
	 * @var ApplicationLoader
	 */
	public $ApplicationLoader = null;

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
			$this->DependencyInjector = new OneDI([
				new DependencyCollectionFromConfigDelegate($this->ConfigHandler, self::Config_Dependencies)
			]);
			$this->DependencyInjector->AddInstance($this->ConfigHandler, IConfigHandler::class);
			$this->DependencyInjector->AddInstance($this->ConfigHandler, IConfiguration::class);

			// Register application loader
			$this->ApplicationLoader = $this->DependencyInjector->Autowire(ApplicationLoader::class);

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
			// Core files required for OneCore to function
			$coreFileName = ONECORE_PATH . self::CoreFilePrefix . $className . self::FileSuffix;
			if (file_exists($coreFileName))
				require_once $coreFileName;

			// Common files can be used stand-alone
			$commonFileName = ONECORE_PATH . self::CommonFilePrefix . $className . self::FileSuffix;
			if (file_exists($commonFileName))
				require_once $commonFileName;

			// OneDI
			$oneDI = ONECORE_PATH . 'OneDI' . DIRECTORY_SEPARATOR . $className . self::FileSuffix;
			if (file_exists($oneDI))
				require_once $oneDI;
		});
	}

	/**
	 * Register a handler for internal exceptions
	 */
	private function registerExceptionHandler()
	{
		set_exception_handler(function (Exception $e) {
			$e = !is_a($e, CoreException::class) ? $e : new Exception($e->getMessage());
			$customExceptionHandler = $this->ConfigHandler ? $this->ConfigHandler->Get(self::Config_ExceptionHandler) : null;
			$exceptionHandlerValidation = $this->validateExceptionHandler($customExceptionHandler);
			if ($exceptionHandlerValidation !== true) {
				$this->handleUnhandledException($e, $exceptionHandlerValidation);
			} else {
				call_user_func_array([
					$customExceptionHandler,
					'HandleException'
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
				'There was an error reading or parsing the default configuration (' . self::DefaultConfigFile . ')',
				'You have failed on your quest to properly configure exception handling'
			];

		if (!class_exists($className))
			return ["Selected exception handler '<i>$className</i>' does not exist"];

		if (!OnePHP::ClassImplements($className, IExceptionHandler::class))
			return ["Selected exception handler '<i>$className</i>' does not implement interface '<i>" . IExceptionHandler::class . "</i>'"];

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
	 * Is debug mode enabled
	 * @return bool
	 */
	public static function IsDebug()
	{
		return (bool)self::GetConfig(self::Config_Debug);
	}

	/**
	 * Runs application
	 * @param string|null $applicationName
	 */
	public static function Run($applicationName = null)
	{
		$applicationName = $applicationName ?: self::GetConfig(self::Config_MainApplicationIdentifier);
		if (!self::Instance()->ApplicationLoader->IsLoaded($applicationName)) {
			$applicationContext = self::Instance()->ApplicationLoader->Load($applicationName);
			$applicationContext->Execute();
		}
	}
}

/**
 * Internal exceptions
 */
class CoreException extends Exception
{
}