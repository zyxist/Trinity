<?php
/**
 * The testing application for Trinity Framework
 *
 * @author Tomasz Jędrzejewski
 */

namespace Application\Area;
use \Trinity\Basement\Module as Module;
use \Trinity\Basement\Application as Basement_Application;

/**
 * The configuration class for "Frontend" area.
 *
 * @author Tomasz Jędrzejewski
 */
class Secondary extends Module
{
	public function onInit(Basement_Application $application)
	{
		echo 'rotfl';
	} // end onInit();
} // end Secondary;