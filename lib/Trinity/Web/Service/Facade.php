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
namespace Trinity\Web\Service;
use \Symfony\Component\EventDispatcher\Event;
use \Trinity\Basement\Service as Basement_Service;
use \Trinity\Web\Facade\Manager as Facade_Manager;

/**
 * The facade builder.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
class Facade extends Basement_Service
{
	/**
	 * Preconfigures and initializes the facade manager object.
	 *
	 * @return \Trinity\Web\Facade\Manager
	 */
	public function getObject()
	{
		$manager = new Facade_Manager;
		$application = \Trinity\Basement\Application::getApplication();

		$application->getEventDispatcher()->connect('controller.web.dispatch.end', function(Event $event) use($manager) {
			$facade = $manager->getSelectedFacadeClass();

			if($facade !== null)
			{
				$facadeBrick = $event['manager']->getBrick($facade);
				$facadeBrick->dispatch($event['manager']);
			}
		});

		$application->getEventDispatcher()->connect('controller.web.dispatch.begin', function(Event $event) use($manager) {
			$event['manager']->facade = $manager;
		});

		return $manager;
	} // end getObject();
} // end Facade;