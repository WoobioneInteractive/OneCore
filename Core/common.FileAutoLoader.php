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
		if (!OnePHP::StringEndsWith($directoryPath, DIRECTORY_SEPARATOR))
			$directoryPath .= DIRECTORY_SEPARATOR;

		spl_autoload_register(function($className) use($directoryPath, $pattern) {
			$classFile = $directoryPath . str_ireplace('{class}', $className, $pattern) . self::FileSuffix;

			if (file_exists($classFile))
				require_once $classFile;
		});
	}
}