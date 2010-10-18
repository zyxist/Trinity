<?php
/**
 * The testing application for Trinity Framework
 *
 * @author Tomasz Jędrzejewski
 */

namespace Application\Frontend;
use \Trinity\Web\Area;

class Module extends Area
{
	protected $_metadata = array(
		'controllerService' => 'GroupController',
		'defaultGroup' => 'index',
		'defaultArea' => 'index'
	);

	/**
	 * Returns the area name.
	 *
	 * @return string
	 */
	public function getAreaName()
	{
		return 'frontend';
	} // end getAreaName();
} // end Module;