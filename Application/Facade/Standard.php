<?php
/**
 * The testing application for Trinity Framework
 *
 * @author Tomasz Jędrzejewski
 */
namespace Application\Facade;
use \Trinity\Web\Brick;
use \Trinity\Web\Controller\Manager as Controller_Manager;
use \Trinity\Web\Facade\Manager as Facade_Manager;

/**
 * The default facade executed for every action.
 */
class Standard extends Brick
{

	protected function _dispatch(Controller_Manager $manager)
	{
		$navigationView = $manager->getView('Trinity.Opt.View.Navigation');
		$navigationView->addModel('navigation', $manager->services->get('Navigation'));

		return $navigationView;
	} // end _dispatch();
} // end Standard;