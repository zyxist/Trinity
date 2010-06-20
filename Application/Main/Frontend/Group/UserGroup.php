<?php
/**
 * The testing application for Trinity Framework
 *
 * @author Tomasz Jędrzejewski
 */
use Trinity\WebUtils\View\Grid as GridView;
use Trinity\WebUtils\Controller\Action_Group as ControllerActionGroup;

class UserGroup extends ControllerActionGroup
{
	public function indexAction()
	{
		$view = new GridView($this->getApplication());
		$view->set('args', $this->getRequest()->getParams());
		$view->set('title', 'User list');
		$view->addModel('grid', $this->getModel('Application.Main.Model.User'));

		return $view;
	} // end indexAction();
} // end UserGroup;