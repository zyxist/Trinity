<?php
/**
 * The testing application for Trinity Framework
 *
 * @author Tomasz Jędrzejewski
 */
namespace Application\Main\Frontend\Group;
use \Trinity\Web\Controller\Manager;
use \Trinity\WebUtils\View\Grid;
use \Trinity\WebUtils\Controller\Action_Group as ControllerActionGroup;

class IndexGroup extends ControllerActionGroup
{
	public function indexAction(Manager $manager)
	{
		$fooNs = $manager->session->getNamespace('foo');
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

		$view = $this->getActionView();

		$view->addModel('date', $manager->getModel('Application.Main.Model.CurrentDate'));
		$view->addModel('session', $fooNs);

		return $view;
	} // end indexAction();

	public function listAction(Manager $manager)
	{
		$view = $manager->getView('Trinity.WebUtils.View.Grid');
		$view->set('args', $manager->request->getParams());
		$view->set('title', 'Some dummy title');
		$view->addModel('grid', $manager->getModel('Application.Main.Model.Grid'));

		return $view;
	} // end listAction();

	public function brickAction(Manager $manager)
	{
		$brick = $manager->getBrick('Application.Main.Brick.Test');
		$brick->dispatch();

		return $this->getActionView();
	} // end brickAction();
} // end IndexGroup;