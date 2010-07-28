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
use \Trinity\Web\Controller_Exception;
use \Trinity\Web\Controller\Manager;
use \Trinity\Web\Controller as Web_Controller;
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
	 * The brick action mapping.
	 * @var array
	 */
	private $_brickActions = array();

	/**
	 * The group name.
	 * @var string.
	 */
	private $_groupName;

	/**
	 * The default group view
	 * @var \Trinity\WebUtils\View\ActionGroup
	 */
	private $_view = false;

	/**
	 * The action that is expected to be run.
	 *
	 * @var string
	 */
	private $_actionName = null;

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
	 * @param \Trinity\Web\Controller\Manager $manager The controller manager.
	 * @param \Trinity\WebUtils\Controller\Group $controller The controller that dispatches the request.
	 * @param string The action that is expected to be run.
	 */
	public function __construct(Manager $manager, Group $controller, $action)
	{
		if(!preg_match('/\\\\([a-zA-Z0-9]+)Group$/', get_class($this), $matches))
		{
			throw new Controller_Exception('Cannot instantiate bare Action_Group class.');
		}
		$this->_manager = $manager;
		$this->_controller = $controller;
		$this->_groupName = $matches[1];
		$this->_actionName = $action;

		$this->init();
	} // end __construct();

	/**
	 * Here the programmer may store various custom group-related code.
	 */
	public function init()
	{
		/* null */
	} // end init();

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
	 * Registers a new brick action. The specified action will be directly
	 * mapped to an external brick, so that the programmer does not have
	 * to create a special method for it.
	 *
	 * @param string $actionName The action name
	 * @param string $brickName The fully qualified brick name.
	 */
	public function addBrickAction($actionName, $brickName)
	{
		$this->_brickActions[$actionName] = (string)$brickName;
	} // end addBrickAction();

	/**
	 * Checks if the specified action is a brick action.
	 *
	 * @param string $actionName The action name to check
	 * @return boolean
	 */
	public function hasBrickAction($actionName)
	{
		return isset($this->_brickActions[$actionName]);
	} // end hasBrickAction();

	/**
	 * Returns the name of the brick associated with the action.
	 *
	 * @param string $actionName The action name.
	 * @return string
	 */
	public function getBrickAction($actionName)
	{
		if(!isset($this->_brickActions[$actionName]))
		{
			throw new Controller_Exception('The specified brick action: '.$actionName.' does not exist.');
		}
		return $this->_brickActions[$actionName];
	} // end getBrickAction();

	/**
	 * Checks if the specified action exists in the action group.
	 *
	 * @param string $actionName The action name.
	 * @return boolean
	 */
	public function hasAction($actionName)
	{
		return isset($this->_brickActions[$actionName]) || method_exists($this, $actionName.'Action');
	} // end hasAction();

	/**
	 * Returns and optionally constructs the action group view accompanying
	 * the group.
	 * 
	 * @param string $name The attribute name.
	 * @return \Trinity\WebUtils\View\ActionGroup
	 */
	public function getActionView()
	{
		if($this->_view === false)
		{
			$className = $this->_controller->getGroupModule()->getClassName($this->_groupName.'View');
			if($this->_controller->getGroupModule()->loadFile($this->_groupName.'View') && class_exists($className, false))
			{
				$this->_view = new $className($this->_manager->application);
				if(!$this->_view instanceof \Trinity\WebUtils\View\ActionGroup)
				{
					throw new Controller_Exception('The loaded view class is not an instance of \Trinity\WebUtils\View\ActionGroup');
				}
				$this->_view->bind($this->_groupName, $this->_actionName);
			}
			else
			{
				$this->_view = null;
			}
		}
		return $this->_view;
	} // end getActionView();
} // end Action_Group;