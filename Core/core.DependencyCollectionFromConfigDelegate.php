<?php

/**
 * Class DependencyCollectionFromConfigDelegate
 * @uses OnePHP
 */
class DependencyCollectionFromConfigDelegate implements IDependencyCollectionDelegate
{
	/**
	 * @var IConfiguration
	 */
	private $configuration;

	/**
	 * @var string
	 */
	private $parentKey;

	/**
	 * DependencyCollectionFromConfigDelegate constructor.
	 * @param IConfiguration $configuration
	 */
	public function __construct(IConfiguration $configuration, $parentKey)
	{
		$this->configuration = $configuration;
		$this->parentKey = $parentKey;
	}

	/**
	 * Get full dependency map from config
	 * @return array
	 */
	private function getMap()
	{
		return $this->configuration->Get($this->parentKey, []);
	}

	/**
	 * See if collection contains definition for $identifier
	 * @param string $identifier
	 * @return bool
	 */
	public function HasDefinition($identifier)
	{
		return array_key_exists($identifier, $this->getMap());
	}

	/**
	 * Get definition for $identifier
	 * @param string $identifier
	 * @return IDependencyDefinition
	 */
	public function GetDefinition($identifier)
	{
		$definitionMap = OnePHP::ValueIfExists($identifier, $this->getMap());
		return $definitionMap ? new DependencyMapping($definitionMap, $identifier) : null;
	}


}