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
namespace Trinity\Basement\Service;
use \Trinity\Basement\Exception;
use \Trinity\Basement\ServiceLocator;

/**
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
abstract class Container implements ContainerInterface
{
	/**
	 * Self-reflection object.
	 * @var ReflectionObject
	 */
	protected $_reflection;

	/**
	 * The service map constructed using the reflection.
	 * @var array
	 */
	protected $_serviceMap = array();

	/**
	 * This method can be used to define service aliases.
	 *
	 * @return array
	 */
	public function getServiceAliases()
	{
		return array();
	} // end getServiceAliases();

	/**
	 * Returns the names of the provided services.
	 *
	 * @throws \Trinity\Basement\Exception
	 * @param ServiceLocator $locator The service locator instance.
	 * @return array
	 */
	public function getProvidedServices()
	{
		$this->_reflection = new \ReflectionObject($this);

		$aliases = $this->getServiceAliases();
		if(is_array($aliases))
		{
			foreach($aliases as $serviceName => $aliasedMethod)
			{
				if(!method_exists($this, $aliasedMethod))
				{
					throw new Exception('Cannot create an alias for service \''.$serviceName.'\': method '.$aliasedMethod.' is not defined.');
				}
				$this->_serviceMap[$serviceName] = $aliasedMethod;
			}
		}

		$serviceList = array();
		foreach($this->_reflection->getMethods() as $method)
		{
			if(preg_match('/^get([a-zA-Z0-9\_]+)Service$/', $method->getName(), $matches) && $method->getNumberOfRequiredParameters() == 1)
			{
				$this->_serviceMap[$matches[1]] = $method->getName();
				$serviceList[] = $matches[1];
			}
		}

		return $serviceList;
	} // end getProvidedServices();

	/**
	 * Creates the specified service.
	 *
	 * @param string $name The service name.
	 * @param ServiceLocator $locator The service locator.
	 * @return object
	 */
	public function getService($name, ServiceLocator $locator)
	{
		$methodName = $this->_serviceMap[$name];
		return $this->$methodName($locator);
	} // end getService();

	/**
	 * Returns true, if the specified service is defined by this container.
	 *
	 * @param string $name The service name
	 * @return boolean
	 */
	public function hasService($name)
	{
		return isset($this->_serviceMap[$name]);
	} // end hasService();
} // end Container;