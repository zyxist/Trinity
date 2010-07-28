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
use \Trinity\Basement\Module;
use \Trinity\Web\Controller as Web_Controller;
use \Trinity\Web\Controller\Manager;
use \Trinity\Web\Brick;
use \Trinity\Web\View;
use \Trinity\Web\Controller_Exception;

/**
 * This controller implements an one-step layout with single, self-contained
 * actions.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
class Action extends Web_Controller
{
	/**
	 * Default action.
	 * @var string
	 */
	private $_defaultAction = 'index';

	/**
	 * The module responsible for loading actions.
	 * @var \Trinity\Basement\Module
	 */
	protected $_actionModule;

	/**
	 * Sets the module responsible for loading group classes.
	 *
	 * @param \Trinity\Basement\Module $module
	 */
	public function setActionModule(Module $module)
	{
		$this->_actionModule = $module;
	} // end setActionModule();

	/**
	 * Returns the group module.
	 *
	 * @return \Trinity\Basement\Module
	 */
	public function getActionModule()
	{
		return $this->_actionModule;
	} // end getActionModule();


	/**
	 * Sets the default action name.
	 *
	 * @param string $defaultAction Default action name.
	 */
	public function setDefaults($defaultAction)
	{
		$this->_defaultAction = (string)$defaultAction;
	} // end setDefaults();

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
		if($this->_actionModule === null)
		{
			$this->raiseControllerError($manager, Web_Controller::ERROR_CONFIGURATION);
		}
		$action = $manager->request->getParam('action', $this->_defaultAction);
		$actionProcessed = ucfirst($action).'Action';
		$actionQualified = $this->_actionModule->getClassName($actionProcessed);

		if(!ctype_alnum($action))
		{
			$this->raiseControllerError($manager, Web_Controller::ERROR_VALIDATION);
		}
		// Try to load the action object
		if(!$this->_actionModule->loadFile($actionProcessed) || !class_exists($actionQualified, false))
		{
			$this->raiseControllerError($manager, Web_Controller::ERROR_NOT_FOUND);
		}
		$actionObj = new $actionQualified($manager);

		if(!$actionObj instanceof Brick)
		{
			$this->raiseControllerError($manager);
		}

		$manager->events->fire('controller.action.dispatch', array(
			'brick' => $actionObj,
			'action' => $action
		));

		$actionObj->dispatch();

		$manager->events->fire('controller.action.dispatched', array(
			'brick' => $actionObj,
			'action' => $action
		));
	} // end _dispatch();
} // end Action;