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
use \Doctrine\DBAL\Types\Type;
use \Doctrine\DBAL\DriverManager;
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
	 * Creates the connection object for Doctrine
	 *
	 * @param ServiceLocator $serviceLocator
	 * @return \Doctrine\Common\Cache
	 */
	public function getDoctrineConnectionService(ServiceLocator $serviceLocator)
	{
		$trinityConfig = $serviceLocator->getConfiguration();
		return DriverManager::getConnection(array(
			'driver' => $trinityConfig->get('application.database.driver'),
			'host' => $trinityConfig->get('application.database.host'),
			'user' => $trinityConfig->get('application.database.user'),
			'password' => $trinityConfig->get('application.database.password'),
			'dbname' => $trinityConfig->get('application.database.dbname')
		));
	} // end getDoctrineConnectionService();

	/**
	 * Builds the Doctrine entity manager.
	 * 
	 * @param ServiceLocator $serviceLocator
	 * @return \Doctrine\ORM\EntityManager
	 */
	public function getEntityManagerService(ServiceLocator $serviceLocator)
	{
		$trinityConfig = $serviceLocator->getConfiguration();

		$cache = null;
		if($trinityConfig->get('trinity.doctrine.cache') !== null)
		{
			$cache = $serviceLocator->get('DoctrineCache');
		}

		$config = new Configuration;
		if($cache !== null)
		{
			$config->setMetadataCacheImpl($cache);
			$config->setQueryCacheImpl($cache);
		}

		$driverImpl = $config->newDefaultAnnotationDriver($trinityConfig->get('trinity.doctrine.defaultEntityPath'));
		$config->setMetadataDriverImpl($driverImpl);

		$config->setAutoGenerateProxyClasses((bool)$trinityConfig->get('trinity.doctrine.autogenerateProxyClasses'));
		$config->setProxyDir($trinityConfig->get('trinity.doctrine.proxyDirectory'));
		$config->setProxyNamespace($trinityConfig->get('trinity.doctrine.proxyNamespace'));

		Type::addType('binary', 'Trinity\Doctrine\Type\Binary');

		// Create the entity manager
		$entityManager = EntityManager::create(array(
			'driver' => $trinityConfig->get('application.database.driver'),
			'host' => $trinityConfig->get('application.database.host'),
			'user' => $trinityConfig->get('application.database.user'),
			'password' => $trinityConfig->get('application.database.password'),
			'dbname' => $trinityConfig->get('application.database.dbname')
		), $config);
		$entityManager->getConnection()->getDatabasePlatform()->registerDoctrineTypeMapping('bytea', 'binary');
		return $entityManager;
	} // end getEntityManagerService();
} // end Services;