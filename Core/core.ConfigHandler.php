<?php

class OneCoreConfigHandler {

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

}