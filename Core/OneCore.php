<?php

/**
 * This is the core itself. This is where the magic happens
 * User: Anton Netterwall <netterwall@gmail.com>
 */
class One {

    protected static $instance;
    protected function __construct() {  }
    protected function __clone() {  }

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
     * Private stuff
     */
    private function __construct() {}
    private function __clone() {}

    public static function Self() {

        if (!static::$instance)
            static::$instance = new self();
        return static::$instance;

    }

}