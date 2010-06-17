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

use \Doctrine\ORM\EntityManager;
use \Doctrine\ORM\Configuration;

/**
 * Returns the Doctrine ORM object.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
class Service_Doctrine_ORM extends Service
{
	/**
	 * Returns the Doctrine object.
	 * @return \Doctrine\ORM\EntityManager
	 */
	public function getObject()
	{
		// Add the default entity namespace to the autoloader
		$loader = BaseApplication::getApplication()->getLoader('default');
		if($loader instanceof \Opl_Loader)
		{
			$loader->addLibrary($this->defaultEntityNamespace, $this->defaultEntityPath);
		}

		// Configure the system
		

		// Select caching system
		switch($this->cache)
		{
			case 'apc':
				$cache = new \Doctrine\Common\Cache\ApcCache;
				break;
			case 'memcache':
				$cache = new \Doctrine\Common\Cache\Memcache;
				break;
			case 'xcache':
				$cache = new \Doctrine\Common\Cache\Xcache;
				break;
			default:
				$cache = null;
		}

		// Configure everything
		$config = new Configuration;
		if($cache !== null)
		{
			$config->setMetadataCacheImpl($cache);
			$config->setQueryCacheImpl($cache);
		}
		$driverImpl = $config->newDefaultAnnotationDriver($this->defaultEntityPath);
		$config->setMetadataDriverImpl($driverImpl);
		
		$config->setProxyDir($this->proxyDir);
		$config->setProxyNamespace($this->proxyNamespace);

		// Create the entity manager
		return EntityManager::create($this->connection->toArray(), $config);
	} // end getObject();
} // end Service_Doctrine_ORM;