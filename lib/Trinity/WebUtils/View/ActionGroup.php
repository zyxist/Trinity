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
use \Trinity\Basement\Application as BaseApplication;
use \Trinity\Web\View as WebView;
use \Trinity\Web\View_Html as View_Html;
use \Trinity\Web\Controller_Exception as Web_Controller_Exception;

class ActionGroup extends View_Html
{
	private $_action = 'index';

	private $_group = 'index';

	public function __construct(BaseApplication $application)
	{
		parent::__construct($application);
		$eventManager = $application->getEventManager();

		$view = $this;
		
		$eventManager->addCallback('controller.actionGroup.dispatched', function($args) use($view){
			$view->bind($args['group'], $args['action']);
		});
	} // end __construct();

	public function bind($group, $action)
	{
		$this->_group = strtolower($group);
		$this->_action = $action;

		$this->setTemplate('area.templates:'.$this->_group.'/'.$action.'.tpl');
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
		$this->$actionName();

		$view = $this->getTemplateObject();

		$layout = $this->_application->getServiceLocator()->get('template.Layout');
		$layout->appendView($view);
	} // end dispatch();
} // end ActionGroup;
