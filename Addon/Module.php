<?php
/**
 * The testing application for Trinity Framework
 *
 * @author Tomasz Jędrzejewski
 */

namespace Addon;
use \Trinity\Basement\Module as Basement_Module;

class Module extends Basement_Module
{
	public function launch()
	{
		$serviceLocator = $this->getServiceLocator();
		$areaManager = $serviceLocator->get('AreaManager');
		$areaManager->registerModuleForArea($this, 'addon', 'frontend');
	} // end launch();
} // end Module;