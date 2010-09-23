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
namespace Trinity\Template\Service;
use \Trinity\Basement\Service as Service;
use \Trinity\Basement\Application as BaseApplication;
use \Trinity\Basement\Locator_Object as ObjectLocator;

/**
 * Returns the helper locator.
 *
 * @author Amadeusz "megawebmaster" Starzykiewicz
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
class HelperLocator extends Service
{
	/**
	 * Preconfigures and initializes the configuration object.
	 *
	 * @return ObjectLocator
	 */
	public function getObject()
	{
		$application = BaseApplication::getApplication();
		if(!$application->getServiceLocator()->hasServiceGroup('helper'))
		{
			$application->getServiceLocator()->addServiceGroup('helper', '\Trinity\Template\Helper\Service\\');
		}
		$locator = new ObjectLocator(
			'helperLocator',
			$application->getEventDispatcher(),
			null,
			function($name) use ($application){
				return $application->getServiceLocator()->get('helper.'.ucfirst($name));
			}
		);

		return $locator;
	} // end getObject();
} // end HelperLocator;