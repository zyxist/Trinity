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
namespace Trinity\Navigation;
use \Symfony\Component\EventDispatcher\Event;
use \Trinity\Basement\Service\Container;
use \Trinity\Basement\ServiceLocator;
use \Trinity\Navigation\Loader\PhpLoader;
use \Trinity\Navigation\Loader\XmlLoader;
use \Trinity\Navigation\Manager as Navigation_Manager;

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
			'trinity.navigation.xmlLoader.paths' => '%application.directory%config/navigation/',
			'trinity.navigation.phpLoader.paths' => '%application.directory%config/navigation/',
			'trinity.navigation.loader-service' => 'NavigationXmlLoader',
			'trinity.navigation.meaningful-arguments' => 'id'
		);
	} // end getConfiguration();

	/**
	 * Builds the XML loader for the navigation.
	 * 
	 * @param ServiceLocator $serviceLocator Service locator
	 * @return \Trinity\Navigation\Loader\XmlLoader
	 */
	public function getNavigationXmlLoaderService(ServiceLocator $serviceLocator)
	{
		$args = $serviceLocator->getConfiguration();
		$areaManager = $serviceLocator->get('AreaManager');
		$xmlLoader = new XmlLoader($args->get('trinity.navigation.xmlLoader.paths'));

		if(($area = $areaManager->getActiveArea()) === null)
		{
			$eventDispatcher = $serviceLocator->get('EventDispatcher');
			$eventDispatcher->connect('web.application.modules-discovered', function(Event $event) use($xmlLoader)
			{
				$xmlLoader->setFile($event->get('area')->getAreaName().'.xml');
			});
		}
		else
		{
			$xmlLoader->setFile($area->getAreaName().'.xml');
		}

		return $xmlLoader;
	} // end getNavigationXmlLoaderService();

	/**
	 * Builds the PHP loader for the navigation.
	 *
	 * @param ServiceLocator $serviceLocator Service locator
	 * @return \Trinity\Navigation\Loader\PhpLoader
	 */
	public function getNavigationPhpLoaderService(ServiceLocator $serviceLocator)
	{
		$args = $serviceLocator->getConfiguration();
		$areaManager = $serviceLocator->get('AreaManager');
		$phpLoader = new PhpLoader($args->get('trinity.navigation.phpLoader.paths'));

		if(($area = $areaManager->getActiveArea()) === null)
		{
			$eventDispatcher = $serviceLocator->get('EventDispatcher');
			$eventDispatcher->connect('web.application.modules-discovered', function(Event $event) use($phpLoader)
			{
				$phpLoader->setFile($event->get('area')->getAreaName().'.php');
			});
		}
		else
		{
			$phpLoader->setFile($area->getAreaName().'.php');
		}

		return $phpLoader;
	} // end getNavigationPhpLoaderService();

	/**
	 * Returns the navigation manager service.
	 *
	 * @param ServiceLocator $serviceLocator Service locator
	 * @return \Trinity\Navigation\Manager
	 */
	public function getNavigationService(ServiceLocator $serviceLocator)
	{
		$manager = new Navigation_Manager(
			$serviceLocator->get('Cache'),
			$serviceLocator->get(
				$serviceLocator->getConfiguration()->get('trinity.navigation.loader-service')
			)
		);
		$request = $serviceLocator->get('Request');
		$meaningfulArguments = explode(',', $serviceLocator->getConfiguration()->get('trinity.navigation.meaningful-arguments'));

		return $manager;
	} // end getNavigationService();
} // end Services;