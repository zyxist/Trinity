<?php
/*
 *  TRINITY FRAMEWORK <http://www.invenzzia.org>
 *
 * This file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE. It is also available through
 * WWW at this URL: <http://www.invenzzia.org/license/new-bsd>
 *
 * Copyright (c) Invenzzia Group <http://www.invenzzia.org>
 * and other contributors. See website for details.
 */
namespace Trinity\Basement;
use \Trinity\Basement\Service\ContainerInterface;

/**
 * The service locator provides dependency injection for Trinity.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
class ServiceLocator extends Locator
{
	/**
	 * Available service containers.
	 * @var array
	 */
	protected $_containers;
	/**
	 * Service-to-container mapping to check, where to look for the service.
	 * @var array
	 */
	protected $_serviceToContainer = array();

	/**
	 * The global configuration.
	 * @var \Trinity\Basement\Config
	 */
	protected $_configuration;

	/**
	 * Constructs the service locator object. If the configuration object
	 * is not provided, a new, empty one will be used.
	 * 
	 * @param string $name Service locator name
	 * @param Config $config Optional configuration object
	 */
	public function __construct($name, Config $config = null)
	{
		parent::__construct($name);

		if($config === null)
		{
			$this->_configuration = new Config;
		}
		else
		{
			$this->_configuration = $config;
		}

		$this->_containers = new \SplObjectStorage;
	} // end __construct();

	/**
	 * Registers a new service container.
	 * 
	 * @param ContainerInterface $container The service container.
	 */
	public function registerServiceContainer(ContainerInterface $container)
	{
		$this->_containers->attach($container);

		// Get the configuration provided by this container.
		$opts = $container->getConfiguration();
		if(is_array($opts))
		{
			$this->_configuration->merge($opts);
		}

		// Get the names of services provided by this container.
		$serviceList = $container->getProvidedServices();
		if(is_array($serviceList))
		{
			foreach($serviceList as $serviceName)
			{
				$this->_serviceToContainer[$serviceName] = $container;
			}
		}
	} // end registerServiceContainer();

	/**
	 * Returns the configuration object.
	 * 
	 * @return Config
	 */
	public function getConfiguration()
	{
		return $this->_configuration;
	} // end getConfiguration();

	/**
	 * The action performed, when some object is missing.
	 *
	 * @param string $name The service name.
	 */
	protected function _objectMissing($name)
	{
		if(!isset($this->_serviceToContainer[$name]))
		{
			throw new Exception('Cannot locate the service \''.$name.'\': not registered.');
		}

		$object = $this->_serviceToContainer[$name]->getService($name, $this);
		if(!is_object($object))
		{
			throw new Exception('Cannot locate the service \''.$name.'\': invalid data returned from the service container.');
		}

		$this->_pool[(string)$name] = $object;
	} // end _objectMissing();
} // end ServiceLocator;
