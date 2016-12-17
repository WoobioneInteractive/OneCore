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

	/**
	 * Get data from request
	 * @param string $key
	 * @param string $from Specify where to get data from i.e. Method_Get
	 * @return string
	 */
	public function Data($key = null, $from = null);
}