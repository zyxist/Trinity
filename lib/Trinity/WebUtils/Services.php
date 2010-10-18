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
use \Trinity\WebUtils\Facade\Manager as Facade_Manager;

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
			'trinity.webUtils.flashHelper.sessionGroup' => 'flash'
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
		$manager = new Facade_Manager;

		$application->getEventDispatcher()->connect('controller.web.dispatch.end', function(Event $event) use($manager) {
			$facade = $manager->getSelectedFacadeClass();

			if($facade !== null)
			{
				$facadeBrick = $event->getParameter('manager')->getBrick($facade);
				$facadeBrick->dispatch($event->getParameter('manager'));
			}
		});

		$application->getEventDispatcher()->connect('controller.web.dispatch.begin', function(Event $event) use($manager) {
			$event->getParameter('manager')->facade = $manager;
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
} // end Services;