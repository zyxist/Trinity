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
use \Trinity\Basement\Loader;

/**
 * The service builder uses the external definitions to manage and build
 * services.
 *
 * INFO: This is incomplete.
 * 
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
class Builder implements ContainerInterface
{
	/**
	 * The definition loader.
	 * @var \Trinity\Basement\Loader
	 */
	protected $_loader;

	/**
	 * The definition list.
	 * @var array
	 */
	protected $_definitions;

	/**
	 * Creates the service builder.
	 * 
	 * @param Loader $loader The service definition loader.
	 */
	public function __construct(Loader $loader)
	{
		$this->_loader = $loader;
	} // end __construct();

	public function getConfiguration()
	{
		return $loader->getConfiguration();
	} // end getConfiguration();
	
	public function getProvidedServices()
	{
		$serviceDefinitions = $loader->getDefinitions();

		$names = array();
		foreach($serviceDefinitions as $definition)
		{
			$this->_definitions[$names[] = $definition->getServiceName()] = $definition;
		}

		return $names;
	} // end getProvidedServices();

	public function getService($name, ServiceLocator $locator)
	{

	} // end getService();
	
	public function hasService($name)
	{
		return isset($this->_definitions[$name]);
	} // end hasService();
} // end Builder;