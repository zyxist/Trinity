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
use \Trinity\Web\Controller;
use \Trinity\Web\View;
use \Trinity\Web\Controller_Exception;
use \Trinity\Web\Request_Abstract;
use \Trinity\Web\Response_Abstract;

/**
 * This controller implements an one-step layout with single, self-contained
 * actions.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
class Action extends Controller
{
	/**
	 * Default action.
	 * @var string
	 */
	private $_defaultAction = 'index';

	/**
	 * The directory where actions are located.
	 * @var string
	 */
	protected $_actionDirectory;

	/**
	 * An array of loaded actions.
	 * @var array
	 */
	protected $_loadedActions = array();

	/**
	 * Sets the directory where the group classes could be found.
	 *
	 * @throws Controller_Exception
	 * @param string $directory The directory name.
	 */
	public function setActionDirectory($directory)
	{
		if(!is_dir($directory))
		{
			throw new Controller_Exception('The controller action directory '.$directory.' is not accessible.');
		}
		$this->_actionDirectory = (string)$directory;
	} // end setActionDirectory();

	/**
	 * Returns the group directory.
	 *
	 * @return string
	 */
	public function getActionDirectory()
	{
		return $this->_actionDirectory;
	} // end getActionDirectory();


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
	 * @param Request_Abstract $request The request to dispatch.
	 * @param Response_Abstract $response The sent response.
	 */
	protected function _dispatch(Request_Abstract $request, Response_Abstract $response)
	{
		if($this->_actionDirectory === null)
		{
			throw new Controller_Exception('Cannot dispatch the request: the action directory is not set.');
		}
		$action = $request->getParam('action', $this->_defaultAction);

		$actionObj = $this->_loadAction($action);
		$actionObj->setRequest($request);
		$actionObj->setResponse($response);
		$view = $actionObj->dispatch();

		if($view instanceof View)
		{
			$this->_processView($view);
		}
	} // end _dispatch();

	/**
	 * Loads the specified group object.
	 * @param string $name The group name.
	 * @return Action_Single
	 */
	protected function _loadAction($name)
	{
		$name = ucfirst($name).'Action';

		if(!isset($this->_loadedActions[$name]))
		{
			if(!file_exists($this->_actionDirectory.$name.'.php'))
			{
				throw new Controller_Exception('Action '.$name.' does not exist.');
			}

			require($this->_actionDirectory.$name.'.php');
			$act = new $name($this->_application, $this);
			if(!$act instanceof Action_Single)
			{
				throw new Controller_Exception('Action '.$name.' does not have a proper interface.');
			}
			$this->_loadedActions[$name] = $act;
		}
		return $this->_loadedActions[$name];
	} // end _loadAction();
} // end Action;