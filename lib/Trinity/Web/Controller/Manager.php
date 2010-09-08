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
use \Trinity\Basement\Application as Basement_Application;
use \Trinity\Basement\Locator_Object;
use \Trinity\Web\Controller\State;
use \Trinity\Web\Request;
use \Trinity\Web\Response;
use \Trinity\Web\View_Broker;
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
	 * @var \Trinity\Basement\Locator_Service
	 */
	public $services;

	/**
	 * The configuration
	 * @var \Trinity\Utils\Config
	 */
	public $config;

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
	 * @var \Trinity\Basement\Locator_Object;
	 */
	protected $_modelLocator;

	/**
	 * The view broker.
	 * @var View_Broker
	 */
	protected $_viewBroker;

	/**
	 * The currently loaded models.
	 * @var array
	 */
	private $_models = array();

	public function __construct(Basement_Application $application, Request $request, Response $response, Locator_Object $modelLocator)
	{
		$this->application = $application;
		$this->events = $application->getEventDispatcher();
		$this->services = $application->getServiceLocator();

		$this->request = $request;
		$this->response = $response;

		$this->router = $this->services->get('web.Router');
		$this->config = $this->services->get('utils.Config');
		$this->session = $this->services->get('web.Session');
		$this->area = $this->services->get('web.Area');
		$this->_modelLocator = $modelLocator;
	} // end __construct();

	public function dispose()
	{
		$this->application =
			$this->request =
			$this->response =
			$this->events =
			$this->services =
			$this->config =
			$this->router =
			$this->session =
				null;
	} // end dispose();

	public function getModel($model, $contracts = null)
	{
		return $this->_modelLocator->get($model, $contracts);
	} // end getModel();

	public function getView($view)
	{
		$className = str_replace('.', '\\', $view);
		return new $className($this->application);
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
	 * @return \Trinity\Web\View_Broker
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

	/**
	 * Processes the flash message.
	 *
	 * @param Redirect_Flash $flash The flash redirection
	 */
	protected function _processFlashMessage(Redirect_Flash $flash)
	{
		$session = $this->_application->getServiceLocator()->get('web.Session');
		$ns = $session->getNamespace('flash');
		$ns->message = $flash->getMessage();
		$ns->type = $flash->getType();
		$ns->setLifetime('message', 1);
		$ns->setLifetime('type', 1);
	} // end _processFlashMessage();
} // end Manager;