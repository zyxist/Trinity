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
		$session = $this->getService('web.Session');

		$fooNs = $session->getNamespace('foo');
		if(!isset($fooNs->counter))
		{
			$fooNs->counter = 5;
		}
		else
		{
			$fooNs->counter--;
		}

		if(!isset($fooNs->counter2))
		{
			$fooNs->counter2 = 5;
			$fooNs->setLifetime('counter2', 3);
		}
		else
		{
			$fooNs->counter2--;
		}

		$this->view->addModel('date', $this->getModel('Application.Main.Model.CurrentDate'));
		$this->view->addModel('session', $fooNs);

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
} // end IndexGroup;