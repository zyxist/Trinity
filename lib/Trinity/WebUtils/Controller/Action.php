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
	 * Returns the controller name.
	 *
	 * @return string
	 */
	public function getName()
	{
		return 'action';
	} // end getName();


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
		if($this->_module === null || $this->_area === null)
		{
			$this->raiseControllerError($manager, Web_Controller::ERROR_CONFIGURATION);
		}

		$action = $manager->request->getParam('action', $this->_defaultAction);
		$actionProcessed = ucfirst($action).'Action';
		$actionQualified = $this->_module->getNamespacePrefix().'\\'.ucfirst($this->_area->getAreaName()).'\\Action\\'.$actionProcessed;
		$actionFile = $this->_module->getDirectory().ucfirst($this->_area->getAreaName()).DIRECTORY_SEPARATOR.'Action'.DIRECTORY_SEPARATOR.$actionProcessed.'.php';

		if(!ctype_alnum($action))
		{
			$this->raiseControllerError($manager, Web_Controller::ERROR_VALIDATION);
		}
		// Try to load the action object
		
		if(!file_exists($actionFile))
		{
				$this->raiseControllerError($manager, Web_Controller::ERROR_NOT_FOUND);
		}
		require($actionFile);
		if(!class_exists($actionQualified, false))
		{
				$this->raiseControllerError($manager, Web_Controller::ERROR_NOT_FOUND);
		}
		$actionObj = new $actionQualified($manager);

		if(!$actionObj instanceof Brick)
		{
			$this->raiseControllerError($manager);
		}

		$manager->events->notify(new Event($this, 'controller.dispatch', array(
			'brick' => $actionObj,
			'module' => $this->_actionModule,
			'action' => $action
		)));
		$manager->events->notify(new Event($this, 'controller.action.dispatch', array(
			'brick' => $actionObj,
			'module' => $this->_actionModule,
			'action' => $action
		)));

		$actionObj->dispatch();

		$manager->events->notify(new Event($this, 'controller.action.dispatched', array(
			'brick' => $actionObj,
			'module' => $this->_actionModule,
			'action' => $action
		)));
		$manager->events->notify(new Event($this, 'controller.dispatched', array(
			'brick' => $actionObj,
			'module' => $this->_actionModule,
			'action' => $action
		)));
	} // end _dispatch();
} // end Action;