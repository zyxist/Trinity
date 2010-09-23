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
use \Trinity\Basement\Core\Exception as Core_Exception;
use \Symfony\Component\EventDispatcher\EventDispatcher;
use \Symfony\Component\EventDispatcher\Event;

/**
 * The controller interface.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
interface Controller
{
	public function setModelLocator(Locator_Object $locator);
	public function getModelLocator();
} // end Controller;

/**
 * The model interface.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
interface Model
{

} // end Model;

/**
 * The view abstract class used for whatever we want.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
abstract class View
{
	/**
	 * The list of registered models.
	 * @var array
	 */
	private $_models = array();

	/**
	 * The data passed to the view.
	 * @var array
	 */
	private $_data = array();

	/**
	 * Returns the data with the specified key. If the key does
	 * not exist, it returns NULL.
	 *
	 * @param string $name The data key
	 * @return mixed Value
	 */
	public function get($name)
	{
		if(!isset($this->_data[$name]))
		{
			return null;
		}
		return $this->_data[$name];
	} // end get();

	/**
	 * Assigns the specified value to the key.
	 *
	 * @param string $name The key we want to assign the data to.
	 * @param string $value The new key value.
	 */
	public function set($name, $value)
	{
		$this->_data[$name] = $value;
	} // end set();

	/**
	 * Checks if the specified data key exists.
	 *
	 * @param name $name The key to check
	 * @return boolean True, if the key exists.
	 */
	public function exists($name)
	{
		return isset($this->_data[$name]);
	} // end exists();

	/**
	 * Binds a new model to the view under the specified name.
	 *
	 * @throws Core_Exception
	 * @param string $name The name of the model
	 * @param $model The concrete model object.
	 */
	public function addModel($name, $model)
	{
		if(isset($this->_models[$name]))
		{
			throw new Core_Exception('The model with the name '.$name.' already exists.');
		}
		$this->_models[$name] = $model;
	} // end addModel();

	/**
	 * Checks if there is a model assigned for the specified
	 * name.
	 *
	 * @param string $name The model name.
	 * @return boolean True, if the model name is assigned.
	 */
	public function hasModel($name)
	{
		return isset($this->_models[$name]);
	} // end hasModel();

	/**
	 * Returns the model assigned to the specified name in
	 * the view.
	 *
	 * @throws Core_Exception
	 * @param string $name The model name
	 * @param string $contract The contract that must be passed.
	 * @return Model The model object
	 */
	public function getModel($name, $contract = null)
	{
		if(!isset($this->_models[$name]))
		{
			throw new Core_Exception('The model '.$name.' does not exist.');
		}

		if($contract !== null)
		{
			if(!is_a($this->_models[$name], $contract))
			{
				throw new Core_Exception('The model '.$name.' does not satisfy the contract '.$contract);
			}
		}

		return $this->_models[$name];
	} // end getModel();

	/**
	 * Removes the model assigned to the specified name. If
	 * the name is not assigned, it throws an exception.
	 *
	 * @throws Core_Exception
	 * @param string $name The model name
	 */
	public function removeModel($name)
	{
		if(!isset($this->_models[$name]))
		{
			throw new Core_Exception('The model '.$name.' does not exist.');
		}
		unset($this->_models[$name]);
	} // end removeModel();

	/**
	 * Assigns a model object to the name in the view. If the model
	 * name is already assigned, it overwrites it.
	 *
	 * @param string $name The model name
	 * @param Model $model The model object
	 */
	public function replaceModel($name, Model $model)
	{
		$this->_models[$name] = $model;
	} // end replaceModel();
} // end View;

/**
 * The basic object locator.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
class Locator
{
	/**
	 * The locator name used to fire events.
	 * @var string
	 */
	protected $_name;

	/**
	 * The object pool.
	 * @var array
	 */
	protected $_pool = array();

	/**
	 * The event dispatcher.
	 * @var EventDispatcher
	 */
	protected $_eventDispatcher;

	/**
	 * Creates the locator.
	 * 
	 * @param string $name The locator name
	 * @param EventDispatcher $eventDispatcher The event manager.
	 */
	public function __construct($name, EventDispatcher $eventDispatcher)
	{
		$this->_name = (string)$name;
		$this->_eventDispatcher = $eventDispatcher;

		$eventDispatcher->notify(new Event($this, 'locator.'.$this->_name.'.created'));
	} // end __construct();

	/**
	 * Finalizes the locator.
	 */
	public function __destruct()
	{
		$this->_eventDispatcher->notify(new Event($this, 'locator.'.$this->_name.'.destroyed'));
	} // end __destruct();

	/**
	 * Returns the object locator name.
	 *
	 * @return string
	 */
	public function getName()
	{
		return $this->_name;
	} // end getName();

	/**
	 * Adds a new object to the pool.
	 *
	 * @throws Core_Exception
	 * @param string $name The object name.
	 * @param object $object The object to store.
	 */
	public function set($name, $object)
	{
		$this->_verify($name, $object);
		$this->_pool[(string)$name] = $object;
	} // end set();

	/**
	 * Checks if there is an object registered under the specified name.
	 *
	 * @return boolean
	 */
	public function exists($name)
	{
		return isset($this->_pool[(string)$name]);
	} // end exists();

	/**
	 * Returns the object stored in the locator under the specified name.
	 * If the creator function is specified, the object may be created on
	 * demand if it is not available yet.
	 *
	 * @param string $name The object name.
	 * @return object
	 */
	public function get($name)
	{
		if(!isset($this->_pool[(string)$name]))
		{
			$this->_objectMissing((string)$name);
		}
		return $this->_pool[(string)$name];
	} // end get();

	/**
	 * This method should check if the specified object can be inserted in this
	 * locator.
	 * 
	 * @param object $object The object to verify.
	 */
	protected function _verify($name, $object)
	{
		/* don't verify */
	} // end _verify();

	/**
	 * The action fired when an object is missing. The default implementation
	 * always throws an exception.
	 *
	 * @throws Core_Exception
	 * @param string $name The missing object name.
	 */
	protected function _objectMissing($name)
	{
		throw new Core_Exception('The object '.$name.' is missing in the locator '.$this->_name);
	} // end _objectMissing();
} // end Locator;

/**
 * The object locators are used to manage a pool of singleton objects of the
 * specified type.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
class Locator_Object extends Locator
{
	/**
	 * An optional callback used to create new objects on demand.
	 * @var callback
	 */
	private $_creatorFunc = null;

	/**
	 * The base class or interface that could be stored in this locator.
	 * @var string
	 */
	private $_baseClass = null;

	/**
	 * Creates a new object locator with the specified name and an optional
	 * object creation callback.
	 *
	 * @throws Core_Exception
	 * @param string $name The locator name
	 * @param EventDispatcher $eventDispatcher The event manager.
	 * @param string $baseClass The name of the base class that can be stored in this locator.
	 * @param callback $creatorFunc The optional object creation function.
	 */
	public function __construct($name, EventDispatcher $eventDispatcher, $baseClass, $creatorFunc = null)
	{
		$this->_baseClass = (string)$baseClass;

		if($creatorFunc !== null)
		{
			if(!\is_callable($creatorFunc))
			{
				throw new Core_Exception('The specified object locator creation function is not callable.');
			}
			$this->_creatorFunc = $creatorFunc;
		}

		parent::__construct($name, $eventDispatcher);
	} // end __construct();

	/**
	 * Finalizes the event manager.
	 */
	public function __destruct()
	{
		$this->_eventDispatcher->notify(new Event($this, 'locator.'.$this->_name.'.destroyed'));
	} // end __destruct();

	/**
	 * Sets the new object creator function.
	 *
	 * @throws Core_Exception
	 * @param callback $creatorFunc The new object creation function.
	 */
	public function setCreatorFunc($creatorFunc)
	{
		if(!\is_callable($creatorFunc))
		{
			throw new Core_Exception('The specified object locator creation function is not callable.');
		}
		$this->_creatorFunc = $creatorFunc;
	} // end setCreatorFunc();

	/**
	 * Returns the current object creation function callback.
	 * 
	 * @return callback
	 */
	public function getCreatorFunc()
	{
		return $this->_creatorFunc;
	} // end getCreatorFunc();

	/**
	 * Returns the name of the base class or interface that
	 * could be stored in this locator.
	 *
	 * @return string
	 */
	public function getBaseClass()
	{
		return $this->_baseClass;
	} // end getName();

	/**
	 * Throws an exception, if the specified object does not implement the
	 * locator interface.
	 *
	 * @throws Core_Exception
	 * @param object $object
	 */
	protected function _verify($name, $object)
	{
		if($this->_baseClass !== '')
		{
			if(!\is_a($object, $this->_baseClass))
			{
				throw new Core_Exception('The object registered as '.$name.' does not implement '.$this->_baseClass);
			}
		}
	} // end _verify();

	/**
	 * Asks the creator function to create the missing object.
	 *
	 * @throws Core_Exception
	 * @param string $name The missing object name.
	 */
	protected function _objectMissing($name)
	{
		if($this->_creatorFunc !== null)
		{
			$this->_pool[(string)$name] = \call_user_func($this->_creatorFunc, $name);

			$this->_verify($name, $this->_pool[(string)$name]);

			$this->_eventDispatcher->notify(new Event($this, 'locator.'.$this->_name.'.new',
				array('name' => $name, 'object' => $this->_pool[(string)$name])
			));
		}
		else
		{
			throw new Core_Exception('The specified object '.$name.' is not available within the locator '.$this->_name);
		}
	} // end _objectMissing();
} // end Locator_Object;

/**
 * Represents a service which should construct some object for the system and
 * specify dependencies.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
abstract class Service
{
	/**
	 * The injected service locator.
	 * @var \Trinity\Basement\Locator_Service
	 */
	protected $_serviceLocator;

	/**
	 * The service options.
	 * @var array
	 */
	private $_options;

	/**
	 * The service name.
	 * @var string
	 */
	private $_name;

	/**
	 * Creates a service object.
	 * 
	 * @param Locator_Service $serviceLocator
	 * @param string $name The service name
	 */
	public function __construct(Locator_Service $serviceLocator, $name)
	{
		$this->_serviceLocator = $serviceLocator;
		$this->_name = $name;
	} // end __construct();

	/**
	 * Returns the service name.
	 * @return string
	 */
	final public function getName()
	{
		return $this->_name;
	} // end getName();

	/**
	 * Sets the service options
	 * 
	 * @param array $options The service options
	 */
	public function setOptions(array $options)
	{
		$this->_options = $options;
	} // end setOptions();

	/**
	 * Returns the service options.
	 *
	 * @return array
	 */
	public function getOptions()
	{
		return $this->_options;
	} // end getOptions();

	/**
	 * Returns the service option with the specified name.
	 *
	 * @param string $name The option name
	 * @return mixed
	 */
	public function __get($name)
	{
		if(!isset($this->_options[$name]))
		{
			return null;
		}
		return $this->_options[$name];
	} // end __get();

	/**
	 * Returns an array of service names that should be preloaded. If
	 * there are no serivces to preload, an empty array should be returned.
	 * 
	 * @return array
	 */
	public function toPreload()
	{
		return array();
	} // end toPreload();

	/**
	 * Returns an array of service names that should be postloaded. If
	 * there are no serivces to preload, an empty array should be returned.
	 *
	 * @return array
	 */
	public function toPostload()
	{
		return array();
	} // end toPostload();

	/**
	 * Constructs the object represented by this service.
	 *
	 * @return mixed
	 */
	abstract public function getObject();

	/**
	 * Ensure that this object will always disappear.
	 */
	public function dispose()
	{
		$this->_config = null;
		$this->_serviceLocator = null;
	} // end dispose();
} // end Service;

/**
 * Allows to build service configurators which provide a configuration
 * for the services.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
interface Service_Configurator
{
	/**
	 * Returns an array of options for the given service.
	 * 
	 * @param string $name The service name.
	 * @return array
	 */
	public function getServiceOptions($name);
} // end Service_Configurator;

/**
 * Service builders are used to construct a service automatically, using some
 * rules. We use them, if we do not want to write a separate service class for
 * each object.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
interface Service_Builder
{
	/**
	 * Returns true, if the builder is able to build the specified service.
	 * 
	 * @param string $name The service name
	 * @return boolean
	 */
	public function canBuild($name);

	/**
	 * Builds the service.
	 *
	 * @param string $name The service name.
	 * @param Locator_Service $serviceLocator The service locator.
	 * @return Service
	 */
	public function build($name, Locator_Service $serviceLocator);
} // end Service_Builder;

/**
 * The service locator is the dependency injection manager. It is responsible
 * for discovering the services, initializing them and injecting the configuration.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
class Locator_Service extends Locator
{
	/**
	 * The aliases for the different services.
	 * @var array
	 */
	private $_aliases = array();

	/**
	 * The service container groups used to discover the default services.
	 * @var string
	 */
	private $_serviceGroups = array();

	/**
	 * The list of configurators.
	 * @var array
	 */
	private $_configurators = array();

	/**
	 * The default configurator.
	 * @var string
	 */
	private $_defaultConfigurator;

	/**
	 * The service builder.
	 * @var Service_Builder
	 */
	private $_builder;

	/**
	 * The service to configurator mapping.
	 * @var array
	 */
	private $_serviceConfigurators = array();

	/**
	 * Adds a new service group used for discovering the services. The group
	 * alias should be a valid PHP class prefix together with the namespace
	 * and the optional trailing underscore, if necessary.
	 *
	 * @throws Core_Exception
	 * @param string $groupName The group name
	 * @param string $groupPrefix The group class prefix.
	 * @return Locator_Service Fluent interface.
	 */
	public function addServiceGroup($groupName, $groupPrefix)
	{
		if(isset($this->_serviceGroups[$groupName]))
		{
			throw new Core_Exception('The specified service group '.$groupName.' is already defined in the service locator.');
		}
		$this->_serviceGroups[$groupName] = (string)$groupPrefix;

		return $this;
	} // end addServiceGroup();

	/**
	 * Returns true, if the specified service group is defined.
	 * 
	 * @param string $groupName The group name to check
	 * @return boolean
	 */
	public function hasServiceGroup($groupName)
	{
		return isset($this->_serviceGroups[$groupName]);
	} // end hasServiceGroup();

	/**
	 * Removes the specified service group.
	 *
	 * @throws Core_Exception
	 * @param string $groupName The group name to remove
	 * @return Locator_Service Fluent interface.
	 */
	public function removeServiceGroup($groupName)
	{
		if(!isset($this->_serviceGroups[$groupName]))
		{
			throw new Core_Exception('The specified service group '.$groupName.' does not exist.');
		}
		unset($this->_serviceGroups[$groupName]);
		return $this;
	} // end removeServiceGroup();

	/**
	 * Adds an alias for the given service. If case of requesting the service,
	 * the locator will load the replacement class instead of the original one.
	 *
	 * @throws Core_Exception
	 * @param string $serviceName The service name
	 * @param string $replacementClass The replacement class name
	 * @return Locator_Service Fluent interface.
	 */
	public function addAlias($serviceName, $replacementClass)
	{
		if(isset($this->_aliases[$serviceName]))
		{
			throw new Core_Exception('The specified service alias '.$serviceName.' is already defined in the service locator.');
		}
		$this->_aliases[$serviceName] = (string)$replacementClass;

		return $this;
	} // end addAlias();

	/**
	 * Returns true, if the specified service is aliased.
	 * 
	 * @param string $serviceName The service name to check
	 * @return boolean
	 */
	public function isAlias($serviceName)
	{
		return isset($this->_aliases[$serviceName]);
	} // end isAlias();

	/**
	 * Removes the alias for the given service. The operation has no effect, if
	 * the service has already been loaded. If there is no alias defined for
	 * the given service, an exception is thrown.
	 *
	 * @throws Core_Exception
	 * @param string $serviceName
	 * @return Locator_Service Fluent interface.
	 */
	public function removeAlias($serviceName)
	{
		if(!isset($this->_aliases[$serviceName]))
		{
			throw new Core_Exception('The specified service alias '.$serviceName.' does not exist.');
		}
		unset($this->_aliases[$serviceName]);
		return $this;
	} // end removeAlias();

	/**
	 * Registers a new service configurator under the given name. If the configurator
	 * is already defined, an exception is thrown.
	 *
	 * @throws Core_Exception
	 * @param string $name The configurator name
	 * @param Service_Configurator $configurator The configurator object
	 * @return Locator_Service Fluent interface
	 */
	public function addConfigurator($name, Service_Configurator $configurator)
	{
		if(isset($this->_configurators[$name]))
		{
			throw new Core_Exception('The specified configurator '.$name.' is already defined in the service locator.');
		}
		$this->_configurators[$name] = $configurator;

		return $this;
	} // end addConfigurator();

	/**
	 * Returns true, if the specified configurator is defined.
	 * 
	 * @param string $name The configurator name
	 * @return boolean
	 */
	public function hasConfigurator($name)
	{
		return isset($this->_configurators[$name]);
	} // end hasConfigurator();

	/**
	 * Removes the configurator with the given name. If the configurator
	 * is already defined, an exception is thrown.
	 *
	 * @throws Core_Exception
	 * @param string $name The configurator name.
	 * @return Locator_Service Fluent interface
	 */
	public function removeConfigurator($name)
	{
		if(!isset($this->_configurators[$name]))
		{
			throw new Core_Exception('The specified configurator '.$name.' does not exist.');
		}
		unset($this->_configurators[$name]);
		return $this;
	} // end removeConfigurator();

	/**
	 * Sets the name of the default configurator. The configurator does not have
	 * to exist at the moment of the call, but must be defined later in order to
	 * work.
	 *
	 * @param string $name The configurator name
	 * @return Locator_Service Fluent interface
	 */
	public function setDefaultConfigurator($name)
	{
		$this->_defaultConfigurator = $name;
		return $this;
	} // end setDefaultConfigurator();

	/**
	 * Returns the name of the default configurator.
	 * 
	 * @return string
	 */
	public function getDefaultConfigurator()
	{
		return $this->_defaultConfigurator;
	} // end getDefaultConfigurator();

	/**
	 * Requests using the specified configurator to the specified service instead
	 * of the default configurator. The method has no effect if the service has
	 * already been loaded.
	 *
	 * @param string $configurator The configurator name
	 * @param string $service The service name.
	 * @return Locator_Service Fluent interface.
	 */
	public function useConfigurator($configurator, $service)
	{
		$this->_serviceConfigurators[(string)$service] = (string)$configurator;

		return $this;
	} // end useConfigurator();

	/**
	 * Returns the configurator object for the given service.
	 *
	 * @throws Core_Exception
	 * @param string $serviceName The service name
	 * @return Service_Configurator
	 */
	public function getServiceConfigurator($serviceName)
	{
		$configurator = $this->_defaultConfigurator;
		if(isset($this->_serviceConfigurators[$serviceName]))
		{
			$configurator = $this->_serviceConfigurators[$serviceName];
		}
		if(!isset($this->_configurators[$configurator]))
		{
			throw new Core_Exception('Cannot use the configurator '.$configurator.' for service '.$serviceName.': configurator not defined.');
		}
		return $this->_configurators[$configurator];
	} // end getServiceConfigurator();

	/**
	 * Sets the service builder.
	 * 
	 * @param Service_Builder $builder The builder object.
	 * @return Locator_Service Fluent interface.
	 */
	public function setServiceBuilder(Service_Builder $builder)
	{
		$this->_builder = $builder;
		return $this;
	} // end setServiceBuilder();

	/**
	 * Returns the current service builder.
	 *
	 * @return Service_Builder
	 */
	public function getServiceBuilder()
	{
		return $this->_builder;
	} // end getServiceBuilder();

	/**
	 * Throws an exception, if the specified object does not implement Service
	 * interface.
	 *
	 * @throws Core_Exception
	 * @param string $name
	 * @param object $object
	 */
	protected function _verify($name, $object)
	{
		if(!$object instanceof Service)
		{
			throw new Core_Exception('The specified object '.$name.' is not a valid service.');
		}
	} // end _verify();

	/**
	 * Returns the object represented by a given service. If the object is not
	 * defined, it asks the service for creating it.
	 *
	 * @param string $name Service name
	 * @return mixed
	 */
	public function get($name)
	{
		if(isset($this->_pool[$name]))
		{
			// Return the object created by this service
			return $this->_pool[$name];
		}
		// Discover the service
		$waitsForExecution = new \SplStack;

		$service = $this->_serviceLoad($name);

		// Check the service dependencies in order to load them first.
		$waitsForExecution->push($service);
		$this->_resolveDependencies($service, $waitsForExecution);

		// Execute the bootstraping routines
		while($waitsForExecution !== null)
		{
			$toExecute = $waitsForExecution;
			$waitsForExecution = null;
			foreach($toExecute as $item)
			{
				// Load the service object
				$this->_pool[$name = $item->getName()] = $item->getObject();
				$this->_eventDispatcher->notify(new Event($this, 'locator.'.$name.'.new',
					array('name' => $name, 'object' => $this->_pool[(string)$name])
				));

				$postLoading = $item->toPostload();
				$item->dispose();
				// Add the post-selected hooks.
				if(is_array($postLoading) && sizeof($postLoading) > 0)
				{
					foreach($postLoading as $postloadedServiceName)
					{
						if(!isset($this->_pool[(string)$postloadedServiceName]))
						{
							if($waitsForExecution === null)
							{
								$waitsForExecution = new \SplStack;
							}
							$postloadedService = $this->_serviceLoad($postloadedServiceName);
							$waitsForExecution->push($postloadedService);
							$this->_resolveDependencies($postloadedService, $waitsForExecution);
						}
					}
				}
			}
		}

		// Return the object created by this service.
		return $this->_pool[$name];
	} // end get();

	/**
	 * Resolve dependencies and preload them in the requested order.
	 *
	 * @param Hook $initial Initial hook
	 * @param SplStack $toExecute The extra hooks to execute
	 */
	private function _resolveDependencies(Service $initial, \SplStack $toExecute)
	{
		$toResolve = new \SplQueue;
		$toResolve->enqueue($initial);
		do
		{
			$service = $toResolve->dequeue();
			foreach($service->toPreload() as $dependency)
			{
				if(!isset($this->_pool[$dependency]))
				{
					$service = $this->_serviceLoad($dependency);
					$toResolve->enqueue($service);
					$toExecute->push($service);
				}
			}
		}
		while($toResolve->count() > 0);
	} // end _resolveDependencies();

	/**
	 * Performs a raw service load.
	 *
	 * @param string $name The service name
	 * @return Service Loaded service
	 */
	private function _serviceLoad($name)
	{
		if(isset($this->_aliases[$name]))
		{
			// Obtain the class name from an alias.
			$className = $this->_aliases[$name];
		}
		elseif(null !== $this->_builder && $this->_builder->canBuild($name))
		{
			$service = $this->_builder->build($name, $this);
			$service->setOptions($this->getServiceConfigurator($name)->getServiceOptions($name));

			return $service;
		}
		else
		{
			// Use the service groups to build the service class name.
			$exploded = \explode('.', $name);
			if(!isset($this->_serviceGroups[$exploded[0]]) || sizeof($exploded) < 2)
			{
				throw new Core_Exception('Cannot discover '.$name.': unable to resolve the service name.');
			}
			$className = $this->_serviceGroups[$exploded[0]];
			unset($exploded[0]);
			$className .= implode('/', $exploded);
		}

		// Create the service and inject the configuration.
		$service = new $className($this, $name);
		$this->_verify($name, $service);
		$service->setOptions($this->getServiceConfigurator($name)->getServiceOptions($name));

		return $service;
	} // end _serviceLoad();
} // end Locator_Service;

/**
 * The base application class.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
abstract class Application
{
	/**
	 * The event manager used by the application
	 * @var \Symfony\Component\EventDispatcher\EventDispatcher
	 */
	private $_eventDispatcher;

	/**
	 * The application service locator
	 * @var Locator_Service
	 */
	private $_serviceLocator;

	/**
	 * The application module manager
	 * @var \Trinity\Basement\Module\Manager
	 */
	private $_moduleManager;

	/**
	 * The list of autoloaders.
	 * @var array
	 */
	private $_loaders;

	/**
	 * Currently selected config loader. Default: 'Ini'.
	 * @var string
	 */
	private $_configLoader = 'Ini';
	/**
	 * A list of supported config loaders.
	 * @var array
	 */
	private $_configLoaders = array('Ini', 'Yaml');

	/**
	 * The current application object.
	 * @var Application
	 */
	static private $_application = null;

	/**
	 * Sets the current application object.
	 *
	 * @param Application $application The application object.
	 */
	static public function setApplication(Application $application)
	{
		self::$_application = $application;
	} // end setApplication();

	/**
	 * Returns the current application object.
	 *
	 * @return Application
	 */
	static public function getApplication()
	{
		return self::$_application;
	} // end getApplication();

	/**
	 * Initializes the application.
	 */
	public function initialize()
	{
		self::$_application = $this;

		$this->_eventDispatcher = new EventDispatcher;
		$this->_serviceLocator = new Locator_Service('services', $this->_eventDispatcher);
		$this->_serviceLocator->addServiceGroup('utils', '\Trinity\Utils\Service_');
		$this->_serviceLocator->addServiceGroup('model', '\Trinity\Model\Service_');

		$this->_launch();
	} // end initialize();

	/**
	 * Custom launching procedures.
	 */
	abstract protected function _launch();

	/**
	 * Returns the event dispatcher.
	 *
	 * @return \Symfony\Component\EventDispatcher\EventDispatcher
	 */
	public function getEventDispatcher()
	{
		return $this->_eventDispatcher;
	} // end getEventDispatcher();

	/**
	 * Returns the bootstrap object.
	 *
	 * @return Locator_Service
	 */
	public function getServiceLocator()
	{
		return $this->_serviceLocator;
	} // end getServiceLocator();

	/**
	 * Returns the module manager.
	 *
	 * @return \Trinity\Basement\Module\Manager
	 */
	public function getModuleManager()
	{
		return $this->_moduleManager;
	} // end getModuleManager();

	/**
	 * Installs a new instance of a module manager in the application.
	 *
	 * @param \Trinity\Basement\Module\Manager $manager The new module manager
	 * @return \Trinity\Basement\Application Fluent interface.
	 */
	public function setModuleManager(\Trinity\Basement\Module\Manager $manager)
	{
		$this->_moduleManager = $manager;

		return $this;
	} // end setModuleManager();

	/**
	 * Registers a new autoloader. In order not to reduce flexibility, it
	 * has no limitations over the interfaces, leaving the compatibility task
	 * to the programmer.
	 * 
	 * @param string $name The autoloader name.
	 * @param object $loader The loader object.
	 * @return boolean
	 */
	public function addLoader($name, $loader)
	{
		if(!is_object($loader))
		{
			return false;
		}
		$this->_loaders[(string)$name] = $loader;
		return true;
	} // end addLoader();

	/**
	 * Returns the autoloader object. In order not to reduce flexibility, it
	 * has no limitations over the interfaces, leaving the compatibility task
	 * to the programmer.
	 *
	 * @param string $name The autoloader name.
	 * @return object
	 */
	public function getLoader($name)
	{
		if(!isset($this->_loaders[$name]))
		{
			return null;
		}
		return $this->_loaders[$name];
	} // end getLoader();

	/**
	 * Sets configuration loader classname. Currently supported loaders are
	 * 'Yaml' and 'Ini'.
	 *
	 * @param string $name Loader name
	 */
	public function setConfigLoader($name)
	{
		if(in_array($name, $this->_configLoaders))
		{
			$this->_configLoader = (string)$name;
		}
	} // end setConfigLoader();

	/**
	 * Returns configuration loader classname.
	 *
	 * @return string
	 */
	public function getConfigLoader()
	{
		return $this->_configLoader;
	} // end getConfigLoader();

	/**
	 * Returns classnames of supported loaders.
	 *
	 * @return array
	 */
	public function getSupportedConfigLoaders()
	{
		return $this->_configLoaders;
	} // end getSupportedConfigLoaders();
} // end Application;