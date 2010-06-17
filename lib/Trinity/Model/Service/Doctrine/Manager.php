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


/**
 * Returns the Doctrine Manager object.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
class Service_Doctrine_Manager extends Service
{
	/**
	 * What to preload?
	 * @return array
	 */
	public function toPreload()
	{
		return array('model.Doctrine_ORM');
	} // end toPreload();

	/**
	 * Returns the Doctrine object.
	 * @return \Trinity\Model\Doctrine\Manager
	 */
	public function getObject()
	{
		return new Doctrine_Manager(BaseApplication::getApplication(), $this->_serviceLocator->get('model.Doctrine_ORM'));
	} // end getObject();
} // end Service_Doctrine_Manager;