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

namespace Trinity\Web\Controller;
use \Symfony\Component\EventDispatcher\Event;
use \Trinity\Basement\ObjectLocator;
use \Trinity\Basement\ServiceLocator;
use \Trinity\Web\Controller\State;
use \Trinity\Web\Request;
use \Trinity\Web\Response;
use \Trinity\Web\View\Broker as View_Broker;
use \Trinity\Web\View;

/**
 * The controller manager offers various services for controllers and bricks,
 * including factory methods for views, models, bricks and any other stuff
 * we would need.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
class Manager
{
	/**
	 * Current request.
	 *
	 * @var \Trinity\Web\Request
	 */
	public $request;

	/**
	 * Current response.
	 *
	 * @var \Trinity\Web\Response
	 */
	public $response;

	/**
	 * Current application.
	 *
	 * @var \Trinity\Basement\Application
	 */
	public $application;

	/**
	 * The event manager.
	 * 
	 * @var \Trinity\Basement\EventDispatcher
	 */
	public $events;

	/**
	 * The service locator
	 *
	 * @var \Trinity\Basement\ServiceLocator
	 */
	public $services;

	/**
	 * The router
	 * @var \Trinity\Web\Router
	 */
	public $router;

	/**
	 * The session manager
	 * @var \Trinity\Web\Session
	 */
	public $session;

	/**
	 * The model locator
	 * @var \Trinity\Basement\ObjectLocator;
	 */
	protected $_modelLocator;

	/**
	 * The view broker.
	 * @var View_Broker
	 */
	protected $_viewBroker;

	/**
	 * Other extraordinary fast-access stuff.
	 * @var array
	 */
	protected $_data;

	/**
	 * The currently loaded models.
	 * @var array
	 */
	private $_models = array();

	/**
	 * Creates the controller manager.
	 *
	 * @param Basement_Application $application
	 * @param Request $request
	 * @param Response $response
	 * @param Locator_Object $modelLocator
	 */
	public function __construct(ServiceLocator $serviceLocator, Request $request, Response $response, ObjectLocator $modelLocator)
	{
		$this->events = $serviceLocator->get('EventDispatcher');
		$this->services = $serviceLocator;
		$this->application = $serviceLocator->get('Application');

		$this->request = $request;
		$this->response = $response;

		$this->router = $this->services->get('Router');
		$this->session = $this->services->get('Session');
		$this->areaManager = $this->services->get('AreaManager');
		$this->_modelLocator = $modelLocator;
	} // end __construct();

	/**
	 * Clears the references, allowing the garbage collector to eat this
	 * object.
	 */
	public function dispose()
	{
		$this->application =
			$this->request =
			$this->response =
			$this->events =
			$this->services =
			$this->router =
			$this->session =
				null;
	} // end dispose();

	/**
	 * Introduces the support for custom manager extensions. Allows an assignment
	 * of the new objects that are not supported by default.
	 *
	 * @throws DomainException
	 * @param string $name The name of the field
	 * @param object $value The object to assign
	 */
	public function __set($name, $value)
	{
		if(!is_object($value))
		{
			throw new DomainException('The assigned value must be an object.');
		}
		$this->_data[$name] = $value;
	} // end __set();

	/**
	 * Returns a custom object assigned to the controller manager. An exception
	 * is thrown, if the object does not exist.
	 *
	 * @throws \Trinity\Web\Controller\Exception
	 * @param string $name The name of the object to load.
	 * @return object
	 */
	public function __get($name)
	{
		if(!isset($this->_data[$name]))
		{
			throw new Exception('Cannot load the object with name '.$name);
		}
		return $this->_data[$name];
	} // end __get();

	/**
	 * The model factory. Inspite of creating the models, the factory checks also
	 * the contracts. Basically, we specify the list of interfaces we expect
	 * to be implemented and if any of them is not, an exception is thrown.
	 *
	 * @param string $model The name of the model.
	 * @param string|array $contracts The list of contracts the retrieved model must satisfy.
	 * @return \Trinity\Basement\Model
	 */
	public function getModel($model, $contracts = null)
	{
		$event = $this->events->filter(
			new Event($this, 'model.initialize'),
			$this->_modelLocator->get($model, $contracts)
		);

		return $event->getReturnValue();
	} // end getModel();

	/**
	 * The view factory.
	 *
	 * @param string $view The view name
	 * @return \Trinity\Web\View
	 */
	public function getView($view)
	{
		$className = str_replace('.', '\\', $view);
		return new $className($this->services);
	} // end getView();

	/**
	 * Constructs and returns the specified controller brick and optionally
	 * initializes it with the specified state object.
	 *
	 * @param className $brick The name of the brick class, with backslashes replaced with dots.
	 * @param State $state The optional controller state object
	 * @return \Trinity\Web\Brick
	 */
	public function getBrick($brick, State $state = null)
	{
		$className = str_replace('.', '\\', $brick);

		$brick = new $className($this);
		if($state !== null)
		{
			$brick->setState($state);
		}
		return $brick;
	} // end getBrick();

	/**
	 * Sets the view broker used by this controller.
	 *
	 * @param View_Broker $broker The new view broker.
	 */
	public function setViewBroker(View_Broker $broker)
	{
		$this->_viewBroker = $broker;
	} // end setViewBroker();

	/**
	 * Returns the current view broker.
	 *
	 * @return \Trinity\Web\View\Broker
	 */
	public function getViewBroker()
	{
		return $this->_viewBroker;
	} // end getViewBroker();

	/**
	 * Performs a view processing.
	 *
	 * @param \Trinity\Web\View $view The view to process.
	 */
	public function processView(View $view)
	{
		$broker = $this->getViewBroker();

		if($broker === null)
		{
			$this->setViewBroker($view->getViewBroker());
		}
		else
		{
			$view->setViewBroker($broker);
		}

		$view->dispatch();
	} // end _processView();
} // end Manager;