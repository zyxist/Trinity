<?php
/**
 * The testing application for Trinity Framework
 *
 * @author Tomasz Jędrzejewski
 */
use Trinity\WebUtils\View\Lists as ListView;
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
		$view = new ListView;
		$view->set('args', $this->getRequest()->getParams());
		$view->addModel('list', $this->getModel('Application.Main.Model.List'));

		return $view;
	} // end listAction();
} // end IndexController;