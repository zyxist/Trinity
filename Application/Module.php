<?php
/**
 * The testing application for Trinity Framework
 *
 * @author Tomasz Jędrzejewski
 */

namespace Application;
use \Trinity\Web\Application as Web_Application;

class Module extends Web_Application
{

	public function registerModules()
	{
		$modules = array();
		$modules[] = new \Trinity\WebUtils\Module;
		$modules[] = new \Trinity\Doctrine\Module;
		$modules[] = new \Trinity\Navigation\Module;
		$modules[] = new \Trinity\Opt\Module;
		$modules[] = new \Trinity\Ops\Module;
		
		
	//	$modules[] = new \Trinity\SwiftMailer\Module;
		$modules[] = $this->getAreaModule();
		$modules[] = new \Addon\Module;

		return $modules;
	} // end registerModules();

	public function registerServiceContainer()
	{
		return new Container();
	} // end registerServiceContainer();
} // end Module;