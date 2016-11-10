<?php

define('ONECORE_PATH', dirname(__FILE__) . DIRECTORY_SEPARATOR);

/**
 * This is the core itself. This is where the magic happens
 * Author: Anton Netterwall <netterwall@gmail.com>
 */
class OneCore {

    /**
     * @var OneCore
     */
    protected static $instance = null;

    /**
     * @var bool
     */
    protected $coreLoaded = false;

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
     * No cloning a singleton
     */
    private function __clone() {}

    /**
     * Load everything essential on instantiation
     */
    private function __construct()
    {
        if (!$this->coreLoaded) {
            foreach (glob(ONECORE_PATH . 'core.*.php') as $coreFile) {
                require_once $coreFile;
            }
            $this->coreLoaded = true;
        }
    }

    /**
     * Runs project
     * @param $projectName string
     */
    public function Run($applicationName = null) {

        if (is_null($applicationName))


        echo $applicationName;

    }

    // Quick Helpers

    /**
     * @return OneCoreConfigHandler
     */
    public static function Config() {
        return self::Instance()->ConfigHandler;
    }

}