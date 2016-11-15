<?php

interface IFileAutoLoader
{
	/**
	 * @param string $directoryPath
	 * @param string|null $pattern
	 */
	public function AddFromDirectory($directoryPath, $pattern = null);
}