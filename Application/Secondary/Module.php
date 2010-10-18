<?php
/**
 * The testing application for Trinity Framework
 *
 * @author Tomasz Jędrzejewski
 */

namespace Application\Secondary;
use \Trinity\Web\Area;

class Module extends Area
{
	protected $_metadata = array(
		'controllerService' => 'ActionController',
		'defaultArea' => 'index'
	);

	/**
	 * Returns the area name.
	 *
	 * @return string
	 */
	public function getAreaName()
	{
		return 'secondary';
	} // end getAreaName();
} // end Module;