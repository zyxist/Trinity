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
use \Trinity\Basement\Application as BaseApplication;
use \Trinity\Web\Controller_Exception as Web_Controller_Exception;
use \Trinity\Web\Controller\Manager;
use \Trinity\Web\Request_Abstract;
use \Trinity\Web\Response_Abstract;
// use Trinity\WebUtils\View\ActionGroup as View_ActionGroup;

/**
 * Represents a single action group for the ActionGroup controller.
 * Concrete groups should extend this class.
 *
 * Note for programmers: it is an equivalent of action controllers in
 * other frameworks.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
class Action_Group
{
	/**
	 * The group name.
	 * @var string.
	 */
	private $_groupName;

	/**
	 * The default group view
	 * @var \Trinity\WebUtils\View\ActionGroup
	 */
	private $_view;

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
	 * @var \Trinity\Web\Controller\Manager
	 */
	private $_manager;

	/**
	 * Constructs the group object.
	 *
	 * @param BaseApplication $application The application link.
	 * @param ActionGroup $controller The controller that dispatches the request.
	 */
	public function __construct(Manager $manager, ActionGroup $controller)
	{
		if(!preg_match('/^([a-zA-Z0-9]+)Group$/', get_class($this), $matches))
		{
			throw new Web_Controller_Exception('Cannot instantiate bare Action_Group class.');
		}
		$this->_manager = $manager;
		$this->_controller = $controller;
		$this->_groupName = $matches[1];
	} // end __construct();

	/**
	 * Returns and optionally constructs the default group view object.
	 * 
	 * @param string $name The attribute name.
	 * @return \Trinity\WebUtils\View\ActionGroup
	 */
	public function __get($name)
	{
		if($name == 'view')
		{
			if($this->_view === null)
			{
				$this->_view = $this->_controller->_loadGroupView($this->_groupName);
			}
			return $this->_view;
		}
		return null;
	} // end __get();

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
	 * Returns the group name.
	 *
	 * @return string
	 */
	public function getGroupName()
	{
		return $this->_groupName;
	} // end getGroupName();

	/**
	 * Dispatches the action and returns the view to display.
	 * 
	 * @param string $actionName The action to dispatch.
	 * @return \Trinity\Web\View
	 */
	public function dispatch($actionName)
	{
		if(!ctype_alnum($actionName))
		{
			throw new Web_Controller_Exception('Invalid action name: '.$actionName);
		}

		if(!method_exists($this, $actionName.'Action'))
		{
			throw new Web_Controller_Exception('Action '.$actionName.' does not exist.');
		}
		$actionMethod = $actionName.'Action';

		$this->_manager->events->fire('controller.actionGroup.dispatch', array(
			'groupObj' => $this,
			'group' => $this->_groupName,
			'action' => $actionName
		));

		$view = $this->$actionMethod($this->_manager);

		$this->_manager->events->fire('controller.actionGroup.dispatched', array(
			'groupObj' => $this,
			'group' => $this->_groupName,
			'action' => $actionName
		));

		return $view;
	} // end dispatch();
} // end Action_Group;