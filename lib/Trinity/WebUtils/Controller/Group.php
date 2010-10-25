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
use \Symfony\Component\EventDispatcher\Event;
use \Trinity\Basement\Module;
use \Trinity\Web\Area;
use \Trinity\Web\Controller as Web_Controller;
use \Trinity\Web\View;
use \Trinity\Web\Controller_Exception as Web_Controller_Exception;
use \Trinity\Web\Request as Request_Abstract;
use \Trinity\Web\Response as Response_Abstract;
use \Trinity\Web\Controller\Manager;
use \Trinity\WebUtils\View\ActionGroup as View_ActionGroup;

/**
 * This controller implements a classical two-step layout known from most
 * web frameworks. The primary difference is the naming convention. For the
 * sake of consistency, the "action controllers" are called "groups". Furthermore,
 * due to the availability of different controller patterns and brick
 * functionality, only one action in one group can be called by the controller.
 * In order to re-use the code, it should be packed into bricks.
 * 
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
class Group extends Web_Controller
{
	/**
	 * Default action group.
	 *
	 * @var string
	 */
	private $_defaultGroup = 'index';

	/**
	 * Default action.
	 * @var string
	 */
	private $_defaultAction = 'index';



	/**
	 * Returns the controller name.
	 *
	 * @return string
	 */
	public function getName()
	{
		return 'group';
	} // end getName();

	/**
	 * Sets the default group and action name.
	 * 
	 * @param string $defaultGroup Default group name.
	 * @param string $defaultAction Default action name.
	 */
	public function setDefaults($defaultGroup, $defaultAction)
	{
		$this->_defaultGroup = (string)$defaultGroup;
		$this->_defaultAction = (string)$defaultAction;
	} // end setDefaults();

	/**
	 * Returns the name of the default group.
	 * @return string
	 */
	public function getDefaultGroup()
	{
		return $this->_defaultGroup;
	} // end getDefaultGroup();

	/**
	 * Returns the name of the default action.
	 * @return string
	 */
	public function getDefaultAction()
	{
		return $this->_defaultAction;
	} // end getDefaultAction();

	/**
	 * Dispatches the request.
	 * 
	 * @param \Trinity\Web\Controller\Manager $manager The controller manager.
	 */
	protected function _dispatch(Manager $manager)
	{
		// Validation, etc.
		if($this->_module === null || $this->_area === null)
		{
			$this->raiseControllerError($manager, Web_Controller::ERROR_CONFIGURATION);
		}
		$group = $manager->request->getParam('group', $this->_defaultGroup);
		$groupProcessed = ucfirst($group).'Group';
		$groupQualified = $this->_module->getNamespacePrefix().'\\'.ucfirst($this->_area->getAreaName()).'\\Group\\'.$groupProcessed;
		$groupFile = $this->_module->getDirectory().ucfirst($this->_area->getAreaName()).DIRECTORY_SEPARATOR.'Group'.DIRECTORY_SEPARATOR.$groupProcessed.'.php';
		$action = $manager->request->getParam('action', $this->_defaultAction);

		if(!ctype_alnum($action))
		{
			$this->raiseControllerError($manager, Web_Controller::ERROR_VALIDATION);
		}
		// Try to load the group object
		if(!file_exists($groupFile))
		{
				$this->raiseControllerError($manager, Web_Controller::ERROR_NOT_FOUND);
		}
		require($groupFile);
		if(!class_exists($groupQualified, false))
		{
				$this->raiseControllerError($manager, Web_Controller::ERROR_NOT_FOUND);
		}

		$groupObj = new $groupQualified($manager, $this, $action);

		// Try to dispatch the action.
		
		if(!$groupObj->hasAction($action))
		{
			$this->raiseControllerError($manager, Web_Controller::ERROR_NOT_FOUND);
		}

		$manager->events->notify(new Event($this, 'controller.dispatch', array(
			'groupObj' => $groupObj,
			'group' => $group,
			'action' => $action
		)));
		$manager->events->notify(new Event($this, 'controller.group.dispatch', array(
			'groupObj' => $groupObj,
			'group' => $group,
			'action' => $action
		)));
		if($groupObj->hasBrickAction($action))
		{
			$brick = $manager->getBrick($groupObj->getBrickAction($action));
			$brick->dispatch();
		}
		else
		{
			$actionMethod = $action.'Action';
			$view = $groupObj->$actionMethod($manager);
			if($view instanceof View)
			{
				$manager->processView($view);
			}
		}

		$manager->events->notify(new Event($this, 'controller.group.dispatched', array(
			'groupObj' => $groupObj,
			'group' => $group,
			'action' => $action
		)));
		$manager->events->notify(new Event($this, 'controller.dispatched', array(
			'groupObj' => $groupObj,
			'group' => $group,
			'action' => $action
		)));
	} // end _dispatch();
} // end Group;