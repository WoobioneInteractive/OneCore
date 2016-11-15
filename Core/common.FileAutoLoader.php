<?php

class FileAutoLoader implements IFileAutoLoader
{
	const FileSuffix = '.php';

	/**
	 * @param string $directoryPath
	 * @param string|null $pattern
	 */
	public function AddFromDirectory($directoryPath, $pattern = null)
	{
		spl_autoload_register(function($className) use($directoryPath, $pattern) {
			require_once $directoryPath . str_ireplace('{class}', $className, $pattern) . self::FileSuffix;
		});
	}
}