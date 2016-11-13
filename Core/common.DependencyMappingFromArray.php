<?php

class DependencyMappingFromArray implements IDependencyMapping
{
	/**
	 * @var array
	 */
	private $dependencyMap;

	/**
	 * ArrayDependencyMapping constructor.
	 * @param array $arrayMap
	 */
	public function __construct(Array $arrayMap)
	{
		$this->dependencyMap = $arrayMap;
	}

	/**
	 * @return array
	 */
	public function GetMap()
	{
		return $this->dependencyMap;
	}
}