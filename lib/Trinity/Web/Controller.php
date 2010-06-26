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

namespace Trinity\Web;
use \Trinity\Basement\Controller as CoreController;
use \Trinity\Basement\Locator_Object as ObjectLocator;
use \Trinity\Basement\Application as BaseApplication;

/**
 * The default web controller.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
abstract class Controller implements CoreController
{
	/**
	 * The model locator.
	 * @var ObjectLocator
	 */
	protected $_modelLocator;
	/**
	 * The application link.
	 * @var BaseApplication
	 */
	protected $_application;

	/**
	 * The view broker.
	 * @var View_Broker
	 */
	protected $_viewBroker;

	/**
	 * Initializes the controller.
	 * 
	 * @param BaseApplication $application The application object.
	 */
	public function __construct(BaseApplication $application)
	{
		$this->_application = $application;
	} // end __construct();

	/**
	 * Assigns a new model locator to the controller.
	 *
	 * @param ObjectLocator $locator The model locator
	 */
	public function setModelLocator(ObjectLocator $locator)
	{
		$this->_modelLocator = $locator;
	} // end setModelLocator();

	/**
	 * Returns the current model locator.
	 *
	 * @return ObjectLocator
	 */
	public function getModelLocator()
	{
		return $this->_modelLocator;
	} // end getModelLocator();

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
	 * Dispatches the specified request and response.
	 * 
	 * @param Request_Abstract $request The HTTP request details
	 * @param Response_Abstract $response The HTTP response object
	 */
	public function dispatch(Request_Abstract $request, Response_Abstract $response)
	{
		$eventManager = $this->_application->getEventManager();
		$router = $this->_application->getServiceLocator()->get('web.Router');
		$area = $this->_application->getServiceLocator()->get('web.Area');
		$router->setParam('area', $area->getName());

		$eventManager->fire('controller.web.dispatch.begin', array(
			'controller' => $this,
			'request' => $request,
			'response' => $response
		));

		$router->setParams($request->getParams());
		try
		{
			$this->_dispatch($request, $response);

			$eventManager->fire('controller.web.dispatch.end', array(
				'controller' => $this,
				'request' => $request,
				'response' => $response
			));
		}
		catch(Redirect_Exception $redirect)
		{
			$url = $redirect->getRoute();

			if($redirect instanceof Redirect_Flash)
			{
				$this->_processFlashMessage($redirect);
			}

			$response->setRedirect($url, $redirect->getCode());
			// TODO: Add a true redirection here
			$eventManager->fire('controller.web.dispatch.redirect', array(
				'controller' => $this,
				'request' => $request,
				'response' => $response,
				'redirect' => $redirect
			));
		}
	} // end dispatch();

	/**
	 * Performs a view processing.
	 * 
	 * @param \Trinity\Web\View $view The view to process.
	 */
	protected function _processView(View $view)
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

	/**
	 * The concrete dispatching procedure should go here.
	 *
	 * @throws Redirect_Exception
	 * @param Request_Abstract $request
	 * @param Response_Abstract $response
	 */
	abstract protected function _dispatch(Request_Abstract $request, Response_Abstract $response);
} // end Controller;