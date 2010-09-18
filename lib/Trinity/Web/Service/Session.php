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
use \Trinity\Basement\Application as BaseApplication;
use \Trinity\Basement\Service as Basement_Service;
use \Trinity\Web\Session as Web_Session;

/**
 * Initializes the session service.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
class Session extends Basement_Service
{
	/**
	 * Preconfigures and initializes the session service.
	 *
	 * @return Session
	 */
	public function getObject()
	{
		$application = BaseApplication::getApplication();
		$session = new Web_Session($application);

		$eventDispatcher = $application->getEventDispatcher();
		$eventDispatcher->connect('controller.web.dispatch.begin', function($args) use($session) {
			$session->start();

			return true;
		});

		$eventDispatcher->connect('controller.web.dispatch.end', function($args) use($session) {
			$session->writeClose();

			return true;
		});

		return $session;
	} // end getObject();
} // end Session;