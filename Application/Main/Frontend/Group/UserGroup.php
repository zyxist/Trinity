<?php
/**
 * The testing application for Trinity Framework
 *
 * @author Tomasz Jędrzejewski
 */
namespace Application\Main\Frontend\Group;
use \Trinity\Web\Controller\Manager;
use Trinity\WebUtils\Controller\Action_Group as ControllerActionGroup;

class UserGroup extends ControllerActionGroup
{
	public function indexAction(Manager $manager)
	{
		$view = $manager->getView('Trinity.WebUtils.View.Grid');
		$view->set('args', $manager->request->getParams());
		$view->set('title', 'User list');
		$view->addModel('grid', $manager->getModel('Application.Main.Model.User'));

		return $view;
	} // end indexAction();
} // end UserGroup;