<?php

class CoreExceptionHandler implements IExceptionHandler {

    public static function HandleException(Exception $e) {
		var_dump($e->getMessage());
    }

}