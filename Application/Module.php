<?php
/**
 * The testing application for Trinity Framework
 *
 * @author Tomasz Jędrzejewski
 */

namespace Application;
use \Trinity\Basement\Application as BaseApplication;
use \Trinity\Basement\Module as TrinityModule;

class Module extends TrinityModule
{
	/**
	 * Connect some stuff to the proper elements.
	 *
	 * @param BaseApplication $application The application.
	 */
	public function onInit(BaseApplication $application)
	{
		/* null */
	} // end onInit();
} // end Module;