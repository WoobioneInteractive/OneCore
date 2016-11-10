<?php

define('ONECORE_PATH', dirname(__FILE__) . DIRECTORY_SEPARATOR);

/**
 * This is the core itself. This is where the magic happens
 * Author: Anton Netterwall <netterwall@gmail.com>
 */
class OneCore {

    const Config_Debug = 'core.debug';

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
    public static final function Instance() {
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

            foreach (glob(ONECORE_PATH . 'core.*.php') as $coreFile) {
                require_once $coreFile;
            }

            $this->registerConfigHandler();

            $this->coreLoaded = true;
        }
    }

    /**
     * No cloning a singleton
     */
    private function __clone() {}

    /**
     * Register a handler for internal exceptions
     */
    private function registerExceptionHandler() {
        set_exception_handler(function(Exception $e) {
            $e = !is_a($e, 'CoreException') ? $e : new Exception($e->getMessage());
            call_user_func_array(array(OneCore::GetConfig(OneCore::Config_ExceptionMode) . 'Result', 'HandleException'), array($e));
        });
    }

    /**
     * Register the config handler
     */
    private function registerConfigHandler() {
        if (!$this->ConfigHandler)
            $this->ConfigHandler = new CoreConfigHandler();
    }

    /**
     * Runs project
     * @param $projectName string
     */
    public function Run($applicationName = null) {

        //if (is_null($applicationName))


        echo $applicationName;

    }

    /**
     * Load config file into config handler
     * @param $configFilePath string
     */
    public static function LoadConfig($configFilePath) {
        self::Instance()->ConfigHandler->LoadConfig($configFilePath);
    }

}

/**
 * Internal exceptions
 */
class CoreException extends Exception {}