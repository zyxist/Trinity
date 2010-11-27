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
namespace Trinity\WebUtils\Controller\Group;
use \Trinity\Basement\Application as Basement_Application;
use \Trinity\Web\Controller\Exception;
use \Trinity\Web\Controller\Manager;
use \Trinity\Web\Controller as Web_Controller;
use \Trinity\WebUtils\Controller\Group;

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
class ActionGroup
{
	/**
	 * The brick action mapping.
	 * @var array
	 */
	protected $_brickActions = array();

	/**
	 * The group name.
	 * @var string.
	 */
	protected $_groupName;

	/**
	 * The action that is expected to be run.
	 *
	 * @var string
	 */
	protected $_actionName = null;

	/**
	 * The controller for the action group.
	 * @var \Trinity\WebUtils\Controller\ActionGroup
	 */
	protected $_controller;

	/**
	 * The application link.
	 * @var \Trinity\Web\Controller\Manager
	 */
	protected $_manager;

	/**
	 * Constructs the group object.
	 *
	 * @throws \Trinity\Web\Controller\Exception
	 * @param \Trinity\Web\Controller\Manager $manager The controller manager.
	 * @param \Trinity\WebUtils\Controller\Group $controller The controller that dispatches the request.
	 * @param string The action that is expected to be run.
	 */
	public function __construct(Manager $manager, Group $controller, $action)
	{
		if(!preg_match('/\\\\([a-zA-Z0-9]+)Group$/', get_class($this), $matches))
		{
			throw new Exception('Cannot instantiate bare ActionGroup class.');
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
} // end Action_Group;