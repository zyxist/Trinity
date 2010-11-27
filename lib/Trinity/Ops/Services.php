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
namespace Trinity\Ops;
use \Ops\Auth;
use \Trinity\Basement\Service\Container;
use \Trinity\Basement\ServiceLocator;
use \Trinity\Ops\Auth\Storage\Session as Storage_Session;


/**
 * Defines, how to start the default web stack services and configure them.
 * These services may be overwritten by other containers.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
class Services extends Container
{
	public function getConfiguration()
	{
		return array(
			'trinity.ops.authModel' => null
		);
	} // end getConfiguration();

	/**
	 * Returns the authentication service.
	 *
	 * @param ServiceLocator $serviceLocator Service locator
	 * @return \Ops\Auth
	 */
	public function getAuthService(ServiceLocator $serviceLocator)
	{
		$modeller = $serviceLocator->getConfiguration()->get('trinity.ops.authModel');
		$auth = new Auth;
		$auth->setStorage(new Storage_Session($serviceLocator->get('Session'), new $modeller($serviceLocator)));

		return $auth;
	} // end getAuthService();
} // end Services;
