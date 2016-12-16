<?php

interface IConfiguration
{
	public function Get($configKey, $valueIfNotExists = null);
}
