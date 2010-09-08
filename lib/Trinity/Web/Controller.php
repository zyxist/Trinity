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
use \Symfony\Component\EventDispatcher\Event;
use \Trinity\Basement\Controller as CoreController;
use \Trinity\Basement\Locator_Object;
use \Trinity\Basement\Application as BaseApplication;
use \Trinity\Web\Controller\Manager;
use \Trinity\Web\Controller\State;

/**
 * The default web controller.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
abstract class Controller implements CoreController
{
	const ERROR_GENERIC = 0;
	const ERROR_NOT_FOUND = 1;
	const ERROR_CONFIGURATION = 2;
	const ERROR_INTEGRITY = 3;
	const ERROR_VALIDATION = 4;

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
	 * The name of the brick used, if a 404 error occurs.
	 * @var string
	 */
	protected $_errorBrick = null;

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
	 * Sets the name of the brick used if a controller error occurs.
	 *
	 * @param string $brickName The name of the brick
	 */
	public function setErrorBrick($brickName)
	{
		$this->_errorBrick = (string)$brickName;
	} // end setErrorBrick();

	/**
	 * Returns the name of the brick used if a controller error occurs.
	 */
	public function getErrorBrick()
	{
		return $this->_errorBrick;
	} // end getErrorBrick();

	/**
	 * Dispatches the specified request and response.
	 * 
	 * @param Request $request The HTTP request details
	 * @param Response $response The HTTP response object
	 */
	public function dispatch(Request $request, Response $response)
	{
		$manager = new Manager($this->_application, $request, $response, $this->_modelLocator);
		$manager->router->setParam('area', $manager->area->getName());

		$manager->events->notify(new Event($this, 'controller.web.dispatch.begin', array(
			'controller' => $this,
			'manager' => $manager
		)));

		$manager->router->setParams($request->getParams());
		try
		{
			$this->_dispatch($manager);

			$manager->events->notify(new Event($this,'controller.web.dispatch.end', array(
				'controller' => $this,
				'manager' => $manager
			)));
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
			$manager->events->notify(new Event($this,'controller.web.dispatch.redirect', array(
				'controller' => $this,
				'manager' => $manager,
				'redirect' => $redirect
			)));
		}
	} // end dispatch();

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
	 * @param \Trinity\Web\Controller\Manager $manager The controller manager.
	 */
	abstract protected function _dispatch(Manager $manager);

	/**
	 * Raises an internal controller error. If an error brick is defined, it executes
	 * it, otherwise it throws a controller exception.
	 *
	 * @throws \Trinity\Web\Controller_Exception
	 * @param Manager $manager The controller manager.
	 * @param int $errorType The error type
	 */
	public function raiseControllerError(Manager $manager, $errorType = self::ERROR_GENERIC)
	{
		$messageMap = array(
			self::ERROR_GENERIC => 'internal problem.',
			self::ERROR_NOT_FOUND => 'the requested controller action has not been found.',
			self::ERROR_CONFIGURATION => 'invalid or missing controller configuration.',
			self::ERROR_INTEGRITY => 'controller data integrity problem.',
			self::ERROR_VALIDATION => 'input data validation error.',
		);
		if($this->_errorBrick === null)
		{
			throw new Controller_Exception('A controller error occured: '.$messageMap[$errorType]);
		}
		$state = new State;
		$state->errorType = $errorType;
		$brick = $manager->getBrick($this->_errorBrick, $state);
		$brick->dispatch();
	} // end raiseControllerError();
} // end Controller;