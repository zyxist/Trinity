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
 *
 * $Id$
 */

namespace Trinity\Basement;

/**
 * The controller interface.
 */
interface Controller
{
	public function setModelLocator(ObjectLocator $locator);
	public function getModelLocator();
	public function setViewLocator(ObjectLocator $locator);
	public function getViewLocator();
} // end Controller;

/**
 * The model interface.
 */
interface Model
{

} // end Model;

/**
 * The view abstract class used for whatever we want.
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
	 * @param Model $model The concrete model object.
	 */
	public function addModel($name, Model $model)
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
	 * @return Model The model object
	 */
	public function getModel($name)
	{
		if(!isset($this->_models[$name]))
		{
			throw new Core_Exception('The model '.$name.' does not exist.');
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

	/**
	 * Displays the view.
	 */
	abstract public function display();
} // end View;

/**
 * The basic object locator.
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
	 * The event manager.
	 * @var EventManager
	 */
	protected $_eventManager;

	/**
	 * Creates the locator.
	 * 
	 * @param string $name The locator name
	 * @param EventManager $eventManager The event manager.
	 */
	public function __construct($name, EventManager $eventManager)
	{
		$this->_name = (string)$name;
		$this->_eventManager = $eventManager;

		$this->_eventManager->fire('locator.'.$this->_name.'.created', array('locator' => $this));
	} // end __construct();

	/**
	 * Finalizes the locator.
	 */
	public function __destruct()
	{
		$this->_eventManager->fire('locator.'.$this->_name.'.destroyed', array('locator' => $this));
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
		$this->_verifyObject($object);
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
	protected function _verify($object)
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
	 * @param EventManager $eventManager The event manager.
	 * @param string $baseClass The name of the base class that can be stored in this locator.
	 * @param callback $creatorFunc The optional object creation function.
	 */
	public function __construct($name, EventManager $eventManager, $baseClass, $creatorFunc = null)
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

		parent::__construct($name, $eventManager);
	} // end __construct();

	/**
	 * Finalizes the event manager.
	 */
	public function __destruct()
	{
		$this->_eventManager->fire('locator.'.$this->_name.'.destroyed');
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
	protected function _verify($object)
	{
		if(!\is_a($object, $this->_baseClass))
		{
			throw new Core_Exception('The object registered as '.$name.' does not implement '.$this->_baseClass);
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

			if(!\is_a($this->_pool[(string)$name], $this->_baseClass))
			{
				throw new Core_Exception('The object registered as '.$name.' does not implement '.$this->_baseClass);
			}

			$this->_eventManager->fire('locator.'.$this->_name.'.new',
				array('name' => $name, 'object' => $this->_pool[(string)$name])
			);
		}
		else
		{
			throw new Core_Exception('The specified object '.$name.' is not available within the locator '.$this->_name);
		}
	} // end _objectMissing();
} // end Locator_Object;

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

	public function __construct(Locator_Service $serviceLocator)
	{
		$this->_serviceLocator = $serviceLocator;
	} // end __construct();

	public function setOptions(array $options)
	{
		$this->_options = $options;
	} // end setOptions();

	public function getOptions()
	{
		return $this->_options;
	} // end getOptions();

	public function __get($name)
	{
		if(!isset($this->_options[$name]))
		{
			return null;
		}
		return $this->_options[$name];
	} // end __get();

	public function toPreload()
	{
		return array();
	} // end toPreload();

	public function toPostload()
	{
		return array();
	} // end toPostload();

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

interface Service_Configurator
{
	public function getServiceOptions($name);
} // end Service_Configurator;

interface Service_Builder
{
	public function canBuild($name);
	public function build($name, Locator_Service $serviceLocator);
} // end Service_Builder;

/**
 * The service locator is the dependency injection manager. It is responsible
 * for discovering the services, initializing them and injecting the configuration.
 *
 * @author Tomasz Jędrzejewski
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

	public function addAlias($serviceName, $replacementClass)
	{
		if(isset($this->_aliases[$serviceName]))
		{
			throw new Core_Exception('The specified service alias '.$serviceName.' is already defined in the service locator.');
		}
		$this->_aliases[$serviceName] = (string)$replacementClass;

		return $this;
	} // end addAlias();

	public function isAlias($serviceName)
	{
		return isset($this->_aliases[$serviceName]);
	} // end isAlias();

	public function removeAlias($serviceName)
	{
		if(!isset($this->_aliases[$serviceName]))
		{
			throw new Core_Exception('The specified service alias '.$serviceName.' does not exist.');
		}
		unset($this->_aliases[$serviceName]);
		return $this;
	} // end removeAlias();

	public function addConfigurator($name, Service_Configurator $configurator)
	{
		if(isset($this->_configurators[$name]))
		{
			throw new Core_Exception('The specified configurator '.$name.' is already defined in the service locator.');
		}
		$this->_configurators[$name] = $configurator;

		return $this;
	} // end addConfigurator();

	public function hasConfigurator($name)
	{
		return isset($this->_configurators[$name]);
	} // end hasConfigurator();

	public function removeConfigurator($name)
	{
		if(!isset($this->_configurators[$name]))
		{
			throw new Core_Exception('The specified configurator '.$name.' does not exist.');
		}
		unset($this->_configurators[$name]);
		return $this;
	} // end removeConfigurator();

	public function setDefaultConfigurator($name)
	{
		$this->_defaultConfigurator = $name;
	} // end setDefaultConfigurator();

	public function getDefaultConfigurator()
	{
		return $this->_defaultConfigurator;
	} // end getDefaultConfigurator();

	public function useConfigurator($configurator, $service)
	{
		$this->_serviceConfigurators[$service] = $configurator;
	} // end useConfigurator();

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

	public function setServiceBuilder(Service_Builder $builder)
	{
		$this->_builder = $builder;
		return $this;
	} // end setServiceBuilder();

	public function getServiceBuilder()
	{
		return $this->_builder;
	} // end getServiceBuilder();

	/**
	 * Throws an exception, if the specified object does not implement Service
	 * interface.
	 *
	 * @throws Core_Exception
	 * @param object $object
	 */
	protected function _verify($object)
	{
		if(!$object instanceof Service)
		{
			throw new Core_Exception('The specified object '.$name.' is not a valid service.');
		}
	} // end _verify();

	/**
	 * Discovers the service.
	 */
	public function get($name)
	{
		if(isset($this->_pool[$name]))
		{
			// Return the object created by this service
			return $this->_pool[$name];
		}
		// Discover the service
		$toExecute = new \SplStack;

		$service = $this->_serviceLoad($name);
		$this->_resolveDependencies($service, $toExecute);
		$toExecute->push($service);

		// Execute the bootstraping routines
		foreach($toExecute as $item)
		{
			$this->_pool[$name] = $item->getObject();
			$postLoading = $item->toPostload();
			$item->dispose();
			// Add the post-selected hooks.
			if(is_array($postLoading) && sizeof($postLoading) > 0)
			{
				foreach($postLoading as $postloadedServiceName)
				{
					if(!isset($this->_pool[(string)$postloadedServiceName]))
					{
						$postloadedService = $this->_serviceLoad($postloadedServiceName);
						$this->_resolveDependencies($postloadedService, $toExecute);
						$toExecute->push($postloadedService);
					}
				}
			}
		}

		// Return the object created by this service.
		return $this->_pool[$name];
	} // end _objectMissing();

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
				else
				{
					$toExecute->push($this->_service[$dependency]);
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
		$service = new $className($this);
		$this->_verify($service);
		$service->setOptions($this->getServiceConfigurator($name)->getServiceOptions($name));

		return $service;
	} // end _serviceLoad();
} // end Locator_Service;

/**
 * An event listener interface. Allows to receive events.
 */
interface EventListener
{
	public function dispatchEvent($event, $args);
} // end EventListener;

/**
 * An event subscriber interface. Allows to receive events
 * and specify what events to subscribe.
 */
interface EventSubscriber extends EventListener
{
	public function getSubscribedEvents();
} // end EventSubscriber;

/**
 * The class for managing events and event-based programming.
 */
class EventManager
{
	const REMOVE = 512;

	/**
	 * The primitive callbacks.
	 * @var array
	 */
	private $_callbacks = array();

	/**
	 * The listeners and subscribers.
	 * @var array
	 */
	private $_listeners = array();

	/**
	 * Registers a new event listener.
	 *
	 * @param array $events The events to listen.
	 * @param EventSubscriber $listener The listener to add.
	 */
	public function addListener($events, EventListener $listener)
	{
		$hash = spl_object_hash($listener);

		foreach((array)$events as $event)
		{
			if(!isset($this->_listeners[$event]))
			{
				$this->_listeners[$event] = array();
			}
			$this->_listeners[$event][$hash] = $listener;
		}
	} // end addListener();

	/**
	 * Removes an event listener from the specified events.
	 *
	 * @param array $events The list of events the listener should not receive.
	 * @param EventListener $listener The listener to remove.
	 */
	public function removeListener($events, EventListener $listener)
	{
		$hash = spl_object_hash($listener);

		foreach((array)$events as $event)
		{
			if(isset($this->_listeners[$event][$hash]))
			{
				unset($this->_listeners[$event][$hash]);
			}
		}
	} // end removeListener();

	/**
	 * Registers a new event subscriber.
	 *
	 * @param EventSubscriber $subscriber The subscriber to add.
	 */
	public function addSubscriber(EventSubscriber $subscriber)
	{
		$hash = spl_object_hash($subscriber);

		foreach((array)$subscriber->getSubscribedEvents() as $event)
		{
			if(!isset($this->_listeners[$event]))
			{
				$this->_listeners[$event] = array();
			}
			$this->_listeners[$event][$hash] = $subscriber;
		}
	} // end addSubscriber();

	/**
	 * Removes the specified subscriber from the subscribed events.
	 *
	 * @param EventSubscriber $subscriber The subscriber to remove.
	 */
	public function removeSubscriber(EventSubscriber $subscriber)
	{
		$hash = spl_object_hash($subscriber);

		foreach((array)$subscriber->getSubscribedEvents() as $event)
		{
			if(isset($this->_listeners[$event][$hash]))
			{
				unset($this->_listeners[$event][$hash]);
			}
		}
	} // end removeSubscriber();

	/**
	 * Adds a generic callback for the event. Usually this should be an
	 * anonymous function. The callback might return EventManager::REMOVE
	 * in order to remove it from the list after execution, thus providing
	 * a way to create a callback for a single event dispatch.
	 *
	 * @throws Core_Exception
	 * @param string $event The event name.
	 * @param callback $callback The callback to fire.
	 */
	public function addCallback($event, $callback)
	{
		if(!is_callable($callback))
		{
			throw new Core_Exception('The hook registered for an event '.$event.' must be callable.');
		}

		if(!isset($this->_callbacks[$event]))
		{
			$this->_callbacks[$event] = array();
		}
		$this->_callbacks[$event][] = $callback;
	} // end addCallback();

	/**
	 * Fires the specified event.
	 *
	 * @param string $eventName The name of the event.
	 * @param array $args The optional event params.
	 */
	public function fire($eventName, $args = array())
	{
		if(isset($this->_callbacks[$eventName]))
		{
			foreach($this->_callbacks[$eventName] as $id => $callback)
			{
				if(\call_user_func($callback, $args) == self::REMOVE)
				{
					unset($this->_callbacks[$eventName][$id]);
				}
			}
		}
		if(isset($this->_listeners[$eventName]))
		{
			foreach($this->_listeners[$eventName] as $listener)
			{
				$listener->dispatchEvent($eventName, $args);
			}
		}
	} // end fire();
} // end EventManager;

/**
 * The base application class.
 */
abstract class Application
{
	/**
	 * The event manager used by the application
	 * @var EventManager;
	 */
	private $_eventManager;

	/**
	 * The application service locator
	 * @var Locator_Service
	 */
	private $_serviceLocator;

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
		$this->_application = $application;
	} // end setApplication();

	/**
	 * Returns the current application object.
	 *
	 * @return Application
	 */
	static public function getApplication()
	{
		return $this->_application;
	} // end getApplication();

	/**
	 * Initializes the application.
	 */
	public function initialize()
	{
		$this->_application = $this;

		$this->_eventManager = new EventManager;
		$this->_serviceLocator = new Locator_Service('services', $this->_eventManager);
		$this->_serviceLocator->addServiceGroup('garage', '\Trinity\Garage\Service_');
		$this->_serviceLocator->addServiceGroup('hall', '\Trinity\Hall\Service_');

		$this->_launch();
	} // end initialize();

	abstract protected function _launch();

	/**
	 * Returns the event handler.
	 * @return EventHandler;
	 */
	public function getEventManager()
	{
		return $this->_eventManager;
	} // end getEventManager();

	/**
	 * Returns the bootstrap object.
	 *
	 * @return Locator_Service
	 */
	public function getServiceLocator()
	{
		return $this->_serviceLocator;
	} // end getServiceLocator();
} // end Application;