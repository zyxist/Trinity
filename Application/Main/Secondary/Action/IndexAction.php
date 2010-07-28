<?php
/**
 * The testing application for Trinity Framework
 *
 * @author Tomasz Jędrzejewski
 */
namespace Application\Main\Secondary\Action;
use \Trinity\Web\Controller\Manager;
use \Trinity\Web\Brick;

class IndexAction extends Brick
{

	protected function _dispatch(Manager $manager)
	{
		return new \Application\Main\Secondary\View\Test($manager->application);
	} // end _dispatch();

} // end IndexAction;