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

namespace Trinity\WebUtils\View;
use \Symfony\Component\EventDispatcher\Event;
use \Trinity\Basement\Application as Basement_Application;
use \Trinity\Web\View\Html as View_Html;
use \Trinity\Web\Controller_Exception as Web_Controller_Exception;

class ActionGroup extends View_Html
{
	private $_action = 'index';

	private $_group = 'index';

	public function __construct(Basement_Application $application)
	{
		parent::__construct($application);

		$view = $this;
		
		$application->getEventDispatcher()->connect('controller.actionGroup.dispatched', function(Event $event) use($view){
			$view->bind($event['group'], $event['action']);
		});
	} // end __construct();

	public function bind($group, $action)
	{
		$this->_group = strtolower($group);
		$this->_action = $action;

		$this->setTemplateName('default', 'area.templates:'.$this->_group.'/'.$action.'.tpl');
	} // end setAction();

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
			$layout = $this->_application->getServiceLocator()->get('template.Layout');
			$layout->appendView($returnedView);
		}
	} // end dispatch();
} // end ActionGroup;
