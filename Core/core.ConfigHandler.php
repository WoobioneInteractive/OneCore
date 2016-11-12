<?php

/**
 * Config handler
 */
class ConfigHandler {

    /**
     * @var array
     */
    private $configuration = array();

    /**
     * Configure with settings
     * @param $configuration array
     */
    public function Configure(Array $configuration) {

        $this->configuration = $configuration;

    }


    public function LoadConfig($filePath) {
        //echo $filePath;
    }

    /**
     * Get config key value
     * @param $key string Config_xxx
     */
    public function Get($key) {
        return 'CoreExceptionHandler';
    }

}

/**
 * Class ConfigHandlerException
 */
class ConfigHandlerException extends Exception {}