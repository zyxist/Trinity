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
use \Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * The application class providing the basic services and the general
 * framework structure. It should be extended by concrete applications.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
abstract class Application extends Module
{
	const VERSION = 'EXPERIMENTAL';

	/**
	 * The environment name.
	 * @var string
	 */
	protected $_environment;

	/**
	 * Is debugging enabled?
	 * @var boolean
	 */
	protected $_debug;

	/**
	 * The list of currently loaded modules.
	 * @var array
	 */
	protected $_modules;

	/**
	 * Creates a new application object.
	 * 
	 * @param string $environment The name of the runtime environment
	 * @param boolean $debug Is debug mode enabled?
	 */
	public function __construct($environment, $debug)
	{
		$this->_environment = (string) $environment;
		$this->_debug = (boolean) $debug;
	} // end __construct();

	/**
	 * This method should return an array of modules used by this application.
	 *
	 * @return array
	 */
	abstract public function registerModules();

	/**
	 * Returns and optionally constructs the service locator.
	 *
	 * @return \Trinity\Basement\ServiceLocator
	 */
	public function getServiceLocator()
	{
		if($this->_serviceLocator !== null)
		{
			return $this->_serviceLocator;
		}

		$this->_serviceLocator = new ServiceLocator('service locator');
		$this->_serviceLocator->set('Application', $this);
		$this->_serviceLocator->set('EventDispatcher', new EventDispatcher());

		return $this;
	} // end getServiceLocator();

	/**
	 * Launches the application. Concrete application type-specific classes
	 * should extend it to add some extra launching code.
	 */
	public function launch()
	{
		$this->_modules = $this->registerModules();

		if(!is_array($this->_modules))
		{
			throw new Exception('\Trinity\Basement\Application::registerModules() should return an array of modules.');
		}

		$serviceLocator = $this->getServiceLocator();
		foreach($this->_modules as $module)
		{
			if(!$module instanceof Module)
			{
				throw new Exception(get_class($module).' is not a valid module object.');
			}
			$module->setServiceLocator($serviceLocator);

			if(($serviceContainer = $module->registerServiceContainer()) !== null)
			{
				$serviceLocator->registerServiceContainer($serviceContainer);
			}
		}
		if(($serviceContainer = $this->registerServiceContainer()) !== null)
		{
			$serviceLocator->registerServiceContainer($serviceContainer);
		}
		foreach($this->_modules as $module)
		{
			$module->launch();
		}
	} // end launch();

	/**
	 * Shuts down the application.
	 */
	public function shutdown()
	{
		foreach($this->_modules as $module)
		{
			$module->shutdown();
		}
	} // end shutdown();

	/**
	 * Returns the module for the given name. If the module does not exist, an
	 * exception is thrown.
	 *
	 * @throws \Trinity\Basement\Exception
	 * @param string $name The module name.
	 * @return Module
	 */
	public function getModule($name)
	{
		if(!isset($this->_modules[$name]))
		{
			throw new Exception('The module '.$name.' is not defined.');
		}
		return $this->_modules[$name];
	} // end getModule();

	/**
	 * Returns true, if the module with the given name exists.
	 *
	 * @param string $name Module name
	 * @return boolean
	 */
	public function hasModule($name)
	{
		return isset($this->_modules[$name]);
	} // end hasModule();

	/**
	 * Registers a new module with the given name. If the module already
	 * exists, an exception is thrown.
	 *
	 * @throws \Trinity\Basement\Exception
	 * @param string $name The module name
	 * @param Module $module The module object
	 */
	public function addModule($name, Module $module)
	{
		if(isset($this->_modules[$name]))
		{
			throw new Exception('The module '.$name.' already exists.');
		}
		$this->_modules[$name] = $module;
		$module->setServiceLocator($serviceLocator);
		$module->launch();
	} // end addModule();

	/**
	 * Returns the name of the environment.
	 *
	 * @return string
	 */
	public function getEnvironment()
	{
		return $this->_environment;
	} // end getEnvironment();

	/**
	 * Returns true, if debug mode is enabled.
	 *
	 * @return boolean
	 */
	public function isDebug()
	{
		return $this->_debug;
	} // end isDebug();
} // end Application;