<?php

interface IRouter
{
	public function Get($uri, $do);

	public function Post($uri, $do);

	public function Put($uri, $do);

	public function Patch($uri, $do);

	public function Delete($uri, $do);

	public function Options($uri, $do);

	public function CatchAll($uri, $do);

	public function Match(Array $methodArray, $uri, $do);

	/**
	 * Route
	 */
	public function Route();
}