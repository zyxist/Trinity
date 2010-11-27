<?php
/**
 * The testing application for Trinity Framework
 *
 * @author Tomasz Jędrzejewski
 */

namespace Application;
use \Trinity\Basement\Service\Container as Service_Container;

class Container extends Service_Container
{
	public function getConfiguration()
	{
		return array(
			'trinity.opt.layout' => 'area.layouts:layout',
			'trinity.opt.compileMode' => 0,
		);
	} // end getConfiguration();
} // end Container();