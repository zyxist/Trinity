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
namespace Trinity\WebUtils\Controller;
use Trinity\Basement\Application as BaseApplication;
use Trinity\Web\Controller_Exception;
use Trinity\Web\Request_Abstract;
use Trinity\Web\Response_Abstract;

/**
 * Represents a single action for the Action controller.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
abstract class Action_Single
{
	/**
	 * The action name.
	 * @var string.
	 */
	private $_actionName;
	/**
	 * The request object.
	 * @var \Trinity\Web\Request_Abstract
	 */
	private $_request;

	/**
	 * The response object.
	 * @var \Trinity\Web\Response_Abstract
	 */
	private $_response;

	/**
	 * The controller for the action group.
	 * @var \Trinity\WebUtils\Controller\ActionGroup
	 */
	private $_controller;

	/**
	 * The application link.
	 * @var \Trinity\Basement\Application
	 */
	private $_application;

	/**
	 * Constructs the action object.
	 *
	 * @param BaseApplication $application The application link.
	 * @param Action $controller The controller that dispatches the request.
	 */
	public function __construct(BaseApplication $application, Action $controller)
	{
		if(!preg_match('/^([a-zA-Z0-9]+)Action$/', get_class($this), $matches))
		{
			throw new Web_Controller_Exception('Cannot instantiate bare Action_Single class.');
		}
		$this->_application = $application;
		$this->_controller = $controller;
		$this->_actionName = $matches[1];
	} // end __construct();

	/**
	 * A syntactic sugar that speeds up model loading.
	 *
	 * @param string $model The model name.
	 * @param string $contract The contract the model must pass.
	 * @return \Trinity\Basement\Model
	 */
	public function getModel($model, $contract = null)
	{
		$model = $this->_controller->getModelLocator()->get($model);

		if($contract !== null)
		{
			if(!is_a($model, $contract))
			{
				throw new \Trinity\Utils\Model_Exception('The requested model '.$model.' does not satisfy the contract '.$contract);
			}
		}
		return $model;
	} // end getModel();

	/**
	 * A syntactic sugar for loading the service.
	 *
	 * @param string $name Service name
	 * @return object
	 */
	public function getService($name)
	{
		return $this->_application->getServiceLocator()->get($name);
	} // end getService();

	/**
	 * Returns the action name.
	 *
	 * @return string
	 */
	public function getActionName()
	{
		return $this->_actionName;
	} // end getActionName();

	/**
	 * Returns the application link.
	 *
	 * @return \Trinity\Basement\Application
	 */
	public function getApplication()
	{
		return $this->_application;
	} // end getApplication();

	/**
	 * Sets the request.
	 *
	 * @param \Trinity\Web\Request_Abstract $request The request.
	 */
	public function setRequest(Request_Abstract $request)
	{
		$this->_request = $request;
	} // end getRequest();

	/**
	 * Returns the request object.
	 *
	 * @return \Trinity\Web\Request_Abstract
	 */
	public function getRequest()
	{
		return $this->_request;
	} // end getRequest();

	/**
	 * Sets the response.
	 *
	 * @param \Trinity\Web\Response_Abstract
	 */
	public function setResponse(Response_Abstract $response)
	{
		$this->_response = $response;
	} // end setResponse();

	/**
	 * Returns the response.
	 *
	 * @return \Trinity\Web\Response_Abstract
	 */
	public function getResponse()
	{
		return $this->_response;
	} // end getResponse();

	/**
	 * Here the action code should go.
	 *
	 * @return \Trinity\Web\View
	 */
	abstract public function dispatch();
} // end Action_Single;