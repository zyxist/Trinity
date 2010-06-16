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
use \Trinity\Web\Controller as WebController;
use \Trinity\Web\View as View;
use \Trinity\Web\Controller_Exception as Web_Controller_Exception;
use \Trinity\Web\Request_Abstract as Request_Abstract;
use \Trinity\Web\Response_Abstract as Response_Abstract;
use \Trinity\WebUtils\View\ActionGroup as View_ActionGroup;

/**
 * This controller implements a classical two-step layout known from most
 * web frameworks. The only difference is that action controller are called
 * action groups for the sake of consistency.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
class ActionGroup extends WebController
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
	 * The directory where groups are located.
	 * @var string
	 */
	protected $_groupDirectory;

	/**
	 * An array of loaded groups.
	 * @var array
	 */
	protected $_loadedGroups = array();

	/**
	 * An array of loaded group views.
	 * @var array
	 */
	protected $_loadedGroupViews = array();

	/**
	 * Sets the directory where the group classes could be found.
	 *
	 * @throws Web_Controller_Exception
	 * @param string $directory The directory name.
	 */
	public function setGroupDirectory($directory)
	{
		if(!is_dir($directory))
		{
			throw new Web_Controller_Exception('The controller group directory '.$directory.' is not accessible.');
		}
		$this->_groupDirectory = (string)$directory;
	} // end setGroupDirectory();

	/**
	 * Returns the group directory.
	 *
	 * @return string
	 */
	public function getGroupDirectory()
	{
		return $this->_groupDirectory;
	} // end getGroupDirectory();

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
	 * @param Request_Abstract $request The request to dispatch.
	 * @param Response_Abstract $response The sent response.
	 */
	protected function _dispatch(Request_Abstract $request, Response_Abstract $response)
	{
		if($this->_groupDirectory === null)
		{
			throw new Web_Controller_Exception('Cannot dispatch the request: the group directory is not set.');
		}
		$group = $request->getParam('group', $this->_defaultGroup);
		$action = $request->getParam('action', $this->_defaultAction);

		$groupObj = $this->_loadGroup($group);
		$groupObj->setRequest($request);
		$groupObj->setResponse($response);
		$view = $groupObj->dispatch($action);

		if($view instanceof View)
		{
			$this->_processView($view);
		}
	} // end _dispatch();

	/**
	 * Loads the specified group object.
	 * @param string $name The group name.
	 * @return Action\Group
	 */
	protected function _loadGroup($name)
	{
		$name = ucfirst($name).'Group';

		if(!isset($this->_loadedGroups[$name]))
		{
			if(!file_exists($this->_groupDirectory.$name.'.php'))
			{
				throw new Web_Controller_Exception('Group '.$name.' does not exist.');
			}

			require($this->_groupDirectory.$name.'.php');
			$grp = new $name($this->_application, $this);
			if(!$grp instanceof Action_Group)
			{
				throw new Web_Controller_Exception('Group '.$name.' does not have a proper interface.');
			}
			$this->_loadedGroups[$name] = $grp;
		}
		return $this->_loadedGroups[$name];
	} // end _loadGroup();

	/**
	 * Loads the group view.
	 * 
	 * @param string $name Group name
	 * @return \Trinity\WebUtils\View\ActionGroup
	 */
	public function _loadGroupView($name)
	{
		$name = ucfirst($name).'View';

		if(!isset($this->_loadedGroupViews[$name]))
		{
			if(!file_exists($this->_groupDirectory.$name.'.php'))
			{
				throw new Web_Controller_Exception('Group view '.$name.' does not exist.');
			}

			require($this->_groupDirectory.$name.'.php');
			$grp = new $name($this->_application, $this);
			if(!$grp instanceof View_ActionGroup)
			{
				throw new Web_Controller_Exception('Group view '.$name.' does not have a proper interface.');
			}
			$this->_loadedGroupViews[$name] = $grp;
		}
		return $this->_loadedGroupViews[$name];
	} // end _loadView();
} // end ActionSet;