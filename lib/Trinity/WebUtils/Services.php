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
namespace Trinity\WebUtils;
use \Symfony\Component\EventDispatcher\Event;
use \Symfony\Component\EventDispatcher\EventDispatcher;
use \Trinity\Basement\Service\Container;
use \Trinity\Basement\ServiceLocator;
use \Trinity\WebUtils\Controller\Action;
use \Trinity\WebUtils\Controller\Group;
use \Trinity\WebUtils\Helper\Flash;
use \Trinity\WebUtils\Helper\Url;
use \Trinity\WebUtils\Facade\Manager as Facade_Manager;
use \Trinity\WebUtils\Translate\CacheWrapper;

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
	 * Returns the default service configuration.
	 * 
	 * @return array
	 */
	public function getConfiguration()
	{
		return array(
			'application.area.default.defaultAction' => 'index',
			'application.area.default.defaultGroup' => 'index',
			'application.area.default.defaultModule' => 'main',
			'trinity.webUtils.flashHelper.sessionGroup' => 'flash',
			'trinity.web.translation.loaderService' => 'XmlLoaderTranslation'
		);
	} // end getConfiguration();

	/**
	 * Creates the action controller.
	 * 
	 * @param ServiceLocator $serviceLocator Service locator.
	 * @return Action
	 */
	public function getActionControllerService(ServiceLocator $serviceLocator)
	{
		$controller = new Action($serviceLocator);
		$controller->setModelLocator($serviceLocator->get('ModelLocator'));
		$controller->setArea($area = $serviceLocator->get('AreaManager')->getActiveArea());
		$controller->setModule($serviceLocator->get('AreaManager')->getActiveModule());

		$controller->setDefaults($area->defaultAction);

		$serviceLocator->set('Controller', $controller);
		$serviceLocator->get('EventDispatcher')->notify(new Event($controller, 'web.controller.created', array('name' => 'action')));

		return $controller;
	} // end getActionControllerService();

	/**
	 * Creates the group controller.
	 * 
	 * @param ServiceLocator $serviceLocator Service locator.
	 * @return Group
	 */
	public function getGroupControllerService(ServiceLocator $serviceLocator)
	{
		$controller = new Group($serviceLocator);
		$controller->setModelLocator($serviceLocator->get('ModelLocator'));
		$controller->setArea($area = $serviceLocator->get('AreaManager')->getActiveArea());
		$controller->setModule($serviceLocator->get('AreaManager')->getActiveModule());

		$controller->setDefaults($area->defaultGroup, $area->defaultAction);

		$serviceLocator->set('Controller', $controller);
		$serviceLocator->get('EventDispatcher')->notify(new Event($controller, 'web.controller.created', array('name' => 'group')));

		return $controller;
	} // end getGroupControllerService();

	/**
	 * Creates the facade manager that adds the MVC stack to the overall layout
	 * configuration.
	 * 
	 * @param ServiceLocator $serviceLocator The service locator.
	 * @return Facade_Manager
	 */
	public function getFacadeService(ServiceLocator $serviceLocator)
	{
		$eventDispatcher = $serviceLocator->get('EventDispatcher');
		$manager = new Facade_Manager;

		$eventDispatcher->connect('controller.web.dispatch.end', function(Event $event) use($manager) {
			$facade = $manager->getSelectedFacadeClass();

			if($facade !== null)
			{
				$facadeBrick = $event->get('manager')->getBrick($facade);
				$facadeBrick->dispatch($event->get('manager'));
			}
		});

		$eventDispatcher->connect('controller.web.dispatch.begin', function(Event $event) use($manager) {
			$event->get('manager')->facade = $manager;
		});

		return $manager;
	} // end getFacadeService();

	/**
	 * Creates the flash helper.
	 *
	 * @param ServiceLocator $serviceLocator The service locator.
	 * @return Flash
	 */
	public function getFlashHelperService(ServiceLocator $serviceLocator)
	{
		$session = $serviceLocator->get('Session');
		return new Flash($session->getGroup($serviceLocator->getConfiguration()->get('trinity.webUtils.flashHelper.sessionGroup')));
	} // end getFlashHelperService();


	public function getXmlLoaderTranslationService(ServiceLocator $serviceLocator)
	{
		$config = $serviceLocator->getConfiguration();
		if($config->isDefined('trinity.web.translation.loader.xml.paths'))
		{
			$loader = new \Opc\Translate\XmlLoader($config->get('trinity.web.translation.loader.xml.paths'));
		}
		else
		{
			$areaManager = $serviceLocator->get('AreaManager');
			$application = $serviceLocator->get('Application');
			$area = $areaManager->getActiveArea();
			$loader = new \Opc\Translate\XmlLoader(array(
				$areaManager->getActiveModule()->getDirectory().ucfirst($area->getAreaName()).DIRECTORY_SEPARATOR.'languages'.DIRECTORY_SEPARATOR,
				$application->getDirectory().ucfirst($area->getAreaName()).DIRECTORY_SEPARATOR.'languages'.DIRECTORY_SEPARATOR,
				$application->getDirectory().'languages'.DIRECTORY_SEPARATOR,
			));
		}
		return $loader;
	} // end getXmlLoaderTranslationService();

	public function getYamlLoaderTranslationService(ServiceLocator $serviceLocator)
	{
		$config = $serviceLocator->getConfiguration();
		if($config->isDefined('trinity.web.translation.loader.yaml.paths'))
		{
			$loader = new \Opc\Translate\YamlLoader($config->get('trinity.web.translation.loader.yaml.paths'));
		}
		else
		{
			$areaManager = $serviceLocator->get('AreaManager');
			$application = $serviceLocator->get('Application');
			$area = $areaManager->getActiveArea();
			$loader = new \Opc\Translate\YamlLoader(array(
				$areaManager->getActiveModule()->getDirectory().ucfirst($area->getAreaName()).DIRECTORY_SEPARATOR.'languages'.DIRECTORY_SEPARATOR,
				$application->getDirectory().ucfirst($area->getAreaName()).DIRECTORY_SEPARATOR.'languages'.DIRECTORY_SEPARATOR,
				$application->getDirectory().'languages'.DIRECTORY_SEPARATOR,
			));
		}
		return $loader;
	} // end getYamlLoaderTranslationService();

	public function getIniLoaderTranslationService(ServiceLocator $serviceLocator)
	{
		$config = $serviceLocator->getConfiguration();
		if($config->isDefined('trinity.web.translation.loader.ini.paths'))
		{
			$loader = new \Opc\Translate\IniLoader($config->get('trinity.web.translation.loader.ini.paths'));
		}
		else
		{
			$areaManager = $serviceLocator->get('AreaManager');
			$application = $serviceLocator->get('Application');
			$area = $areaManager->getActiveArea();
			$loader = new \Opc\Translate\IniLoader(array(
				$areaManager->getActiveModule()->getDirectory().ucfirst($area->getAreaName()).DIRECTORY_SEPARATOR.'languages'.DIRECTORY_SEPARATOR,
				$application->getDirectory().ucfirst($area->getAreaName()).DIRECTORY_SEPARATOR.'languages'.DIRECTORY_SEPARATOR,
				$application->getDirectory().'languages'.DIRECTORY_SEPARATOR,
			));
		}
		return $loader;
	} // end getIniLoaderTranslationService();

	/**
	 * Constructs the translation service.
	 *
	 * @param ServiceLocator $serviceLocator The service locator.
	 * @return \Opc\Translate
	 */
	public function getTranslationService(ServiceLocator $serviceLocator)
	{
		$args = $serviceLocator->getConfiguration();

		$translation = new \Opc\Translate(
			new CacheWrapper($serviceLocator->get('Cache')),
			$serviceLocator->get($args->get('trinity.web.translation.loaderService'))
		);

		// TODO: Make it more smart in the future.
		foreach($args->get('trinity.web.translation.languages') as $locale => $priority)
		{
			$translation->addLanguage($locale, $priority);
		}
		$opt = $serviceLocator->get('Opt');
		$opt->setTranslationInterface($translation);
		return $translation;
	} // end getTranslationService();
} // end Services;