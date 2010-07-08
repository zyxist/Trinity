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
namespace Trinity\Utils;
use \Trinity\Basement\Application as BaseApplication;
use \Trinity\Basement\Service as Service;
use \Trinity\Utils\Auth\Storage_Session;
use \Ops\Auth;

/**
 * The configuration service for Open Power Security authentication
 * module.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
class Service_Auth extends Service
{
	/**
	 * Preconfigures and initializes the authentication storage.
	 *
	 * @return \Ops\Auth
	 */
	public function getObject()
	{
		$modeller = $this->identityModel;
		$auth = new Auth;
		$auth->setStorage(new Storage_Session($this->_serviceLocator->get('web.Session'), new $modeller(BaseApplication::getApplication())));
		return $auth;
	} // end getObject();
} // end Service_Auth;