<?php

interface IRequest
{
	/**
	 * Get request string: usually /Home/Index or something..
	 * @return string
	 */
	public function GetRequestString();

	/**
	 * Get request method: GET, POST etc.
	 * @return string
	 */
	public function GetMethod();
}