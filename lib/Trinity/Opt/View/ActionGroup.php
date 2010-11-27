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

namespace Trinity\Opt\View;
use \Symfony\Component\EventDispatcher\Event;
use \Trinity\Basement\ServiceLocator;
use \Trinity\Web\Controller\Exception as Web_Controller_Exception;

/**
 * The view class for the ActionGroup extension. The views for group actions
 * are represented as methods of this class that follow the naming pattern
 * 'actionnameAction()'.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
abstract class ActionGroup extends Html
{
	/**
	 * The name of the group to execute.
	 * @var string
	 */
	private $_group = 'index';

	/**
	 * The name of the action to execute.
	 * @var string
	 */
	private $_action = 'index';

	/**
	 * Binds the action and group data from the controller, so that the object
	 * knows what to dispatch.
	 *
	 * @param string $group Group name
	 * @param string $action Action name
	 */
	public function bind($group, $action)
	{
		$this->_group = strtolower($group);
		$this->_action = $action;

		$this->setTemplateName('default', 'current.templates:'.$this->_group.'/'.$action.'.tpl');
	} // end bind();

	/**
	 * Dispatches the action view method and appends the returned OPT template
	 * object to the layout.
	 */
	public function dispatch()
	{
		if(!ctype_alnum($this->_action))
		{
			throw new Web_Controller_Exception('Invalid action name: '.$this->_action);
		}

		if(!method_exists($this, $this->_action.'Action'))
		{
			throw new Web_Controller_Exception('Action '.$this->_action.' does not exist.');
		}
		$actionName = $this->_action.'Action';
		if(($returnedView = $this->$actionName()) !== null)
		{
			$layout = $this->_serviceLocator->get('Layout');
			$layout->appendView($returnedView);
		}
	} // end dispatch();
} // end ActionGroup;
