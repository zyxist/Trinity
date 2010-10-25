<?php
/*
 *  TRINITY FRAMEWORK <http://www.invenzzia.org>
 *
 * This file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE. It is also available through
 * WWW at this URL: <http://www.invenzzia.org/license/new-bsd>
 *
 * Copyright (c) Invenzzia Group <http://www.invenzzia.org>
 * and other contributors. See website for details.
 */
namespace Trinity\Navigation;
use \Symfony\Component\EventDispatcher\Event;
use \Trinity\Basement\Module as Basement_Module;

/**
 * The navigation module. Note that in the future it is going to be developed
 * as a separate project, Open Power Navigation.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
class Module extends Basement_Module
{
	/**
	 * Creates the service container for this module.
	 *
	 * @return Services
	 */
	public function registerServiceContainer()
	{
		return new Services;
	} // end registerServiceContainer();

	public function launch()
	{
		$serviceLocator = $this->getServiceLocator();
		$eventDispatcher = $serviceLocator->get('EventDispatcher');
		$eventDispatcher->connect('controller.dispatched', function(Event $event) use($serviceLocator) {
			$activePageInfo = array();
			$request = $serviceLocator->get('Request');
			$manager = $serviceLocator->get('Navigation');
			$meaningfulArguments = explode(',', $serviceLocator->getConfiguration()->get('trinity.navigation.meaningful-arguments'));
			foreach($event as $name => $value)
			{
				if(is_scalar($value))
				{
					$activePageInfo[$name] = $value;
				}
			}
			foreach($meaningfulArguments as $name)
			{
				$name = trim($name);
				if($request->hasParam($name))
				{
					$activePageInfo[$name] = $request->getParam($name);
				}
			}
			$manager->findActivePage($event->getSubject()->getName(), $activePageInfo);
		});
	} // end launch();
} // end Module;