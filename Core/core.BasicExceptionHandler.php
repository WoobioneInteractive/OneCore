<?php

class BasicExceptionHandler implements IExceptionHandler
{

	public static function HandleException(Exception $e)
	{
		echo get_class($e) . " was thrown: <b>{$e->getMessage()}</b>";
		exit();
	}

}