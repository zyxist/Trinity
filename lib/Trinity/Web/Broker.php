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
use \Trinity\Basement\Application as Basement_Application;

/**
 * Brokers perform the automated construction of request and response
 * objects for controllers.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
abstract class Broker
{
	/**
	 * The request object
	 * @var Request
	 */
	private $_request;

	/**
	 * The response object
	 * @var Response
	 */
	private $_response;

	/**
	 * The application link.
	 * @var \Trinity\Basement\Application
	 */
	protected $_application;

	/**
	 * Constructs the broker object.
	 *
	 * @param Basement_Application $application The application.
	 */
	public function __construct(Basement_Application $application)
	{
		$this->_application = $application;
	} // end __construct();

	/**
	 * Installs an external request within the broker.
	 *
	 * @param Request $request The new request.
	 * @return Broker Fluent interface.
	 */
	public function setRequest(Request $request)
	{
		$this->_request = $request;

		$this->_application->getEventDispatcher()->notify(new Event($this, 'broker.request.set', array(
			'request' => $request
		)));

		return $this;
	} // end setRequest();

	/**
	 * Returns the current request object.
	 *
	 * @return \Trinity\Web\Request
	 */
	public function getRequest()
	{
		return $this->_request;
	} // end getRequest();

	/**
	 * Installs an external response within the broker.
	 *
	 * @param Response $response The new response.
	 * @return Broker Fluent interface.
	 */
	public function setResponse(Response $response)
	{
		$this->_response = $response;

		$this->_application->getEventDispatcher()->notify(new Event($this, 'broker.response.set', array(
			'response' => $response
		)));

		return $this;
	} // end setResponse();

	/**
	 * Returns the current response object.
	 *
	 * @return \Trinity\Web\Response
	 */
	public function getResponse()
	{
		return $this->_response;
	} // end getResponse();
} // end Broker;