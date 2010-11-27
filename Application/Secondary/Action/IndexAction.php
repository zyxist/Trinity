<?php
/**
 * The testing application for Trinity Framework
 *
 * @author Tomasz Jędrzejewski
 */
namespace Application\Secondary\Action;
use \Trinity\Web\Controller\Manager;
use \Trinity\Web\Brick;

class IndexAction extends Brick
{

	protected function _dispatch(Manager $manager)
	{
		return $manager->getView('Application.Secondary.View.Test');
	} // end _dispatch();

} // end IndexAction;