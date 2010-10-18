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
namespace Trinity\Doctrine;
use \Trinity\Basement\Service\Container;
use \Trinity\Basement\ServiceLocator;
use \Doctrine\ORM\Configuration;
use \Doctrine\ORM\EntityManager;
use \Doctrine\ORM\Mapping\Driver\AnnotationDriver;

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
	/**
	 * The default Doctrine configuration.
	 *
	 * @return array
	 */
	public function getConfiguration()
	{
		return array(
			'trinity.doctrine.cache' => null,
			'trinity.doctrine.defaultEntityPath' => null,
			'trinity.doctrine.autogenerateProxyClasses' => true,
			'trinity.doctrine.proxyNamespace' => null,
			'trinity.doctrine.proxyDirectory' => null,
		);
	} // end getConfiguration();

	/**
	 * Creates the caching object for Doctrine.
	 *
	 * @param ServiceLocator $serviceLocator
	 * @return \Doctrine\Common\Cache
	 */
	public function getDoctrineCacheService(ServiceLocator $serviceLocator)
	{
		switch($serviceLocator->getConfiguration()->get('trinity.doctrine.cache'))
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
		return $cache;
	} // end getDoctrineCacheService();

	/**
	 * Builds the Doctrine entity manager.
	 * 
	 * @param ServiceLocator $serviceLocator
	 * @return \Doctrine\ORM\EntityManager
	 */
	public function getEntityManagerService(ServiceLocator $serviceLocator)
	{
		$config = $serviceLocator->getConfiguration();

		if($config->get('trinity.doctrine.cache') !== null)
		{
			$cache = $serviceLocator->get('DoctrineCache');
		}

		$config = new Configuration;
		if($cache !== null)
		{
			$config->setMetadataCacheImpl($cache);
			$config->setQueryCacheImpl($cache);
		}

		$driverImpl = $config->newDefaultAnnotationDriver($config->get('trinity.doctrine.defaultEntityPath'));
		$config->setMetadataDriverImpl($driverImpl);

		$config->setAutoGenerateProxyClasses((bool)$config->get('trinity.doctrine.autogenerateProxyClasses'));
		$config->setProxyDir($config->get('trinity.doctrine.proxyDirectory'));
		$config->setProxyNamespace($config->get('trinity.doctrine.proxyNamespace'));

		// Create the entity manager
		return EntityManager::create($this->connection->toArray(), $config);
	} // end getEntityManagerService();
} // end Services;