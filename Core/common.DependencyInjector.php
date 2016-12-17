<?php

/**
 * DependencyInjector by Woobione Interactive
 * @author Anton Netterwall <netterwall@gmail.com>
 * @version 1.0-beta
 * @uses IDependencyMapping [Mapping contract for DependencyInjector]
 * @uses DependencyMappingFromArray
 * @uses OnePHP [Helper functions for OneCore]
 */
class DependencyInjector
{
	// Configuration options
	const Config_Mapping = 'dependencyinjector.mapping';

	// Mapping keys
	const Mapping_ResolveTo = 'dependencyinjectormapping.resolveTo';
	const Mapping_LoadOnlyOnce = 'dependencyinjectormapping.loadOnlyOnce';
	const Mapping_RemoteInstance = 'dependencyinjectormapping.remoteInstance';

	// Internal constants
	const InterfacePrefix = 'I';
	const DependencyMappingInterface = 'IDependencyMapping';
	const DependencyInjectableInterface = 'IDependencyInjectable';

	/**
	 * @var IDependencyMapping[]
	 */
	private $dependencyMappings = [];

	/**
	 * @var array
	 */
	private $loadedDependencies = [];

	/**
	 * DependencyInjector constructor.
	 * @param IDependencyMapping|array|null $dependencyMapping
	 */
	public function __construct($dependencyMapping = null)
	{
		if (OnePHP::ClassImplements($dependencyMapping, self::DependencyMappingInterface))
			$this->AddMapping($dependencyMapping);
		else if (is_array($dependencyMapping))
			$this->AddMapping(new DependencyMappingFromArray($dependencyMapping));
		else if (!is_null($dependencyMapping))
			throw new DependencyInjectorException('Invalid dependency mapping supplied in DependencyInjector constructor');
	}

	/**
	 * @param $interfaceName string
	 * @return mixed|null|object
	 * @throws DependencyInjectorException
	 */
	private function resolve($interfaceName)
	{
		// Return already loaded instance
		$loadedInstance = OnePHP::ValueIfExists($interfaceName, $this->loadedDependencies, false);
		if ($loadedInstance)
			return $loadedInstance;

		// Read mapping
		$dependencyMapping = $this->getMap($interfaceName);

		// Resolve dependency
		$resolveTo = OnePHP::ValueIfExists(self::Mapping_ResolveTo, $dependencyMapping);
		$remoteInstance = OnePHP::ValueIfExists(self::Mapping_RemoteInstance, $dependencyMapping, false);
		$loadOnlyOnce = OnePHP::ValueIfExists(self::Mapping_LoadOnlyOnce, $dependencyMapping, true);
		$dependencyInstance = $remoteInstance ?: $this->AutoWire($resolveTo);

		if (is_callable($dependencyInstance))
			$dependencyInstance = $dependencyInstance($this);

		if (!is_a($dependencyInstance, $interfaceName))
			throw new DependencyInjectorException("Resolved instance for '$interfaceName' does not implement said interface");

		// Store dependency instance
		if ($loadOnlyOnce)
			$this->loadedDependencies[$interfaceName] = $dependencyInstance;

		// Return dependency
		return $dependencyInstance;
	}

	/**
	 * Get dependency map for interface
	 * @param $interfaceName string
	 * @return array
	 */
	private function getMap($interfaceName)
	{
		// Find mapping from mappings
		foreach ($this->dependencyMappings as $dependencyMapping) {
			$map = OnePHP::ValueIfExists($interfaceName, $dependencyMapping->GetMap(), false);
			if ($map)
				return $map;
		}

		// Fall back to default
		return [
			self::Mapping_ResolveTo => ltrim($interfaceName, self::InterfacePrefix)
		];
	}

	/**
	 * Add mapping to dependency mappings
	 * - takes precedence over previous mappings
	 * @param IDependencyMapping $dependencyMapping
	 */
	public function AddMapping(IDependencyMapping $dependencyMapping)
	{
		if (!is_null($dependencyMapping))
			array_unshift($this->dependencyMappings, $dependencyMapping);
	}

	/**
	 * Add mapping to dependency mappings
	 * - takes precedence over previous mappings
	 * @param $dependencyMappingClassName string
	 */
	public function AddAutowiredMapping($dependencyMappingClassName)
	{
		array_unshift($this->dependencyMappings, $this->AutoWire($dependencyMappingClassName));
	}

	/**
	 * @param $className string
	 */
	public function AutoWire($className)
	{
		if (!class_exists($className))
			throw new DependencyInjectorException("Failed to find class '$className' when autowiring");

		$class = new ReflectionClass($className);
		$constructor = $class->getConstructor();
		$parameters = $constructor ? $constructor->getParameters() : [];

		// TODO: Change to foreach - supposed to be faster
		array_walk($parameters, function (&$parameter) {
			$parameterReflection = $parameter->getClass();
			$interfaceName = $parameterReflection ? $parameterReflection->getName() : null;

			if (interface_exists($interfaceName) || OnePHP::ClassImplements($interfaceName, self::DependencyInjectableInterface)) {
				$parameter = $this->resolve($interfaceName);
			} else if ($interfaceName == __CLASS__) {
				$parameter = $this;
			} else {
				if (!$parameter->isDefaultValueAvailable() && !$parameter->allowsNull())
					throw new DependencyInjectorException("Failed to autowire class '{$parameter->getDeclaringClass()->getName()}' - unable to resolve property '\${$parameter->getName()}'");

				$parameter = null;
			}
		});

		return $class->newInstanceArgs($parameters);
	}

}

/**
 * Internal exception
 */
class DependencyInjectorException extends Exception
{
}