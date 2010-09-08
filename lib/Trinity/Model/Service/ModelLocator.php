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
namespace Trinity\Model;
use \Trinity\Basement\Service as Service;
use \Trinity\Basement\Application as BaseApplication;
use \Trinity\Basement\Locator_Object as ObjectLocator;

/**
 * Returns the model locator.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
class Service_ModelLocator extends Service
{
	/**
	 * Preconfigures and initializes the configuration object.
	 *
	 * @return ObjectLocator
	 */
	public function getObject()
	{
		$locator = new ObjectLocator(
			'modelLocator',
			BaseApplication::getApplication()->getEventDispatcher(),
			'\Trinity\Basement\Model',
			function($name){
				$className = str_replace('.', '\\', $name);

				return new $className(BaseApplication::getApplication());
			}
		);

		// Preload the model interfaces.
		spl_autoload_call('\\Trinity\\Model\\Interfaces');

		return $locator;
	} // end getObject();
} // end Service_ModelLocator;