<?php
/**
 * The testing application for Trinity Framework
 *
 * @author Tomasz Jędrzejewski
 */
namespace Application\Main\Facade;
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
		
	} // end _dispatch();
} // end Standard;