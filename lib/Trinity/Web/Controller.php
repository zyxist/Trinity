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
use \Trinity\Basement\Locator_Object;
use \Trinity\Basement\Application as BaseApplication;
use \Trinity\Web\Controller\Manager;

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
	 * @var \Trinity\Basement\Locator_Object
	 */
	protected $_modelLocator;
	/**
	 * The application link.
	 * @var \Trinity\Basement\Application
	 */
	protected $_application;



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
	 * @param \Trinity\Basement\Locator_Object $locator The model locator
	 */
	public function setModelLocator(Locator_Object $locator)
	{
		$this->_modelLocator = $locator;
	} // end setModelLocator();

	/**
	 * Returns the current model locator.
	 *
	 * @return \Trinity\Basement\Locator_Object
	 */
	public function getModelLocator()
	{
		return $this->_modelLocator;
	} // end getModelLocator();

	/**
	 * Dispatches the specified request and response.
	 * 
	 * @param Request_Abstract $request The HTTP request details
	 * @param Response_Abstract $response The HTTP response object
	 */
	public function dispatch(Request_Abstract $request, Response_Abstract $response)
	{
		$manager = new Manager($this->_application, $request, $response, $this->_modelLocator);
		$manager->router->setParam('area', $manager->area->getName());

		$manager->events->fire('controller.web.dispatch.begin', array(
			'controller' => $this,
			'manager' => $manager
		));

		$manager->router->setParams($request->getParams());
		try
		{
			$this->_dispatch($manager);

			$manager->events->fire('controller.web.dispatch.end', array(
				'controller' => $this,
				'manager' => $manager
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
			$manager->events->fire('controller.web.dispatch.redirect', array(
				'controller' => $this,
				'manager' => $manager,
				'redirect' => $redirect
			));
		}
	} // end dispatch();

	/**
	 * The concrete dispatching procedure should go here.
	 *
	 * @throws Redirect_Exception
	 * @param \Trinity\Web\Controller\Manager $manager The controller manager.
	 */
	abstract protected function _dispatch(Manager $manager);
} // end Controller;