<?php

interface IPlugin {

	/**
	 * @return string[]|bool Names of plugins the are required by the plugin | bool false
	 */
	public function Dependencies();

}