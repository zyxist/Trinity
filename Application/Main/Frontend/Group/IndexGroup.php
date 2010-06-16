<?php
/**
 * The testing application for Trinity Framework
 *
 * @author Tomasz Jędrzejewski
 */
use Trinity\WebUtils\View\Grid as GridView;
use Trinity\WebUtils\Controller\Action_Group as ControllerActionGroup;

class IndexGroup extends ControllerActionGroup
{
	public function indexAction()
	{
		$this->view->addModel('date', $this->getModel('Application.Main.Model.CurrentDate'));

		return $this->view;
	} // end indexAction();

	public function listAction()
	{
		$view = new GridView($this->getApplication());
		$view->set('args', $this->getRequest()->getParams());
		$view->set('title', 'Some dummy title');
		$view->addModel('grid', $this->getModel('Application.Main.Model.Grid'));

		return $view;
	} // end listAction();
} // end IndexController;