<?php
/**
 * The testing application for Trinity Framework
 *
 * @author Tomasz Jędrzejewski
 */
namespace Application\Frontend\Group;
use \Trinity\Web\Controller\Manager;
use \Trinity\Opt\View\Grid;
use \Trinity\Opt\Controller\Group\ActionGroup;

class IndexGroup extends ActionGroup
{
	public function indexAction(Manager $manager)
	{
		$fooNs = $manager->session->getGroup('foo');
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

		$view->addModel('date', $manager->getModel('Application.Model.CurrentDate'));
		$view->addModel('session', $fooNs);

		return $view;
	} // end indexAction();

	public function listAction(Manager $manager)
	{
		$view = $manager->getView('Trinity.WebUtils.View.Grid');
		$view->set('args', $manager->request->getParams());
		$view->set('title', 'Some dummy title');
		$view->addModel('grid', $manager->getModel('Application.Model.Grid'));

		return $view;
	} // end listAction();

	public function brickAction(Manager $manager)
	{
		$brick = $manager->getBrick('Application.Brick.Test');
		$brick->dispatch();

		return $this->getActionView();
	} // end brickAction();
} // end IndexGroup;