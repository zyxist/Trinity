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
namespace Trinity\Navigation\Service\Reader;
use \Trinity\Basement\Service as Basement_Service;
use \Trinity\Basement\Application as Basement_Application;
use \Trinity\Navigation\Reader\Php as Reader_Php;

/**
 * Returns the navigation manager.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
class Php extends Basement_Service
{
	/**
	 * Orders preloading the area object
	 *
	 * @return array
	 */
	public function toPreload()
	{
		return array('web.Area');
	} // end toPreload();

	/**
	 * Preconfigures and initializes the configuration object.
	 *
	 * @return \Trinity\Navigation\Manager
	 */
	public function getObject()
	{
		$application = \Trinity\Basement\Application::getApplication();
		$area = $application->getServiceLocator()->get('web.Area');

		require($this->readerPath.$area->getName().'.php');

		if(!is_array($navigation))
		{
			throw new \Trinity\Navigation\Exception('Cannot load the navigation from a PHP file for the specified area.');
		}

		return new Reader_Php($area->getName(), $navigation);
	} // end getObject();
} // end Php;
