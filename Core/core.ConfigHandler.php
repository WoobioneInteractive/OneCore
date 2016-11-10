<?php

class CoreConfigHandler {

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
        echo $filePath;
    }

}