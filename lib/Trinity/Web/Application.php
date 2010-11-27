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

namespace Trinity\Web;
use \Symfony\Component\EventDispatcher\Event;
use \Trinity\Basement\Application as Basement_Application;
use \Trinity\Basement\ServiceLocator;

/**
 * The basic application code for writing web applications.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
abstract class Application extends Basement_Application
{
	/**
	 * The area module, when not using strategies.
	 * @var Area
	 */
	protected $_areaModule = null;

	/**
	 * This method can be used by the entry script to hard-code area selection
	 * without a strategy.
	 *
	 * @param Area $area The area object.
	 */
	public function setAreaModule(Area $area)
	{
		$this->_areaModule = $area;
	} // end setAreaModule();

	/**
	 * Returns the area module, when not using strategies.
	 *
	 * @return Area
	 */
	public function getAreaModule()
	{
		return $this->_areaModule;
	} // end getAreaModule();

	/**
	 * Returns and optionally constructs the service locator.
	 *
	 * @return \Trinity\Basement\ServiceLocator
	 */
	public function getServiceLocator()
	{
		if($this->_serviceLocator !== null)
		{
			return $this->_serviceLocator;
		}

		$this->_serviceLocator = new ServiceLocator('service locator');

		$config = $this->_serviceLocator->getConfiguration();
		$config->set('application.directory', $this->getDirectory());

		$this->_serviceLocator->set('Application', $this);
		$this->_serviceLocator->registerServiceContainer(new Services);

		return $this->_serviceLocator;
	} // end getServiceLocator();

	/**
	 * Launches the web application.
	 */
	public function launch()
	{
		// Do the ordinary stuff
		parent::launch();

		// Now run the web MVC stack.
		$serviceLocator = $this->getServiceLocator();
		$eventDispatcher = $serviceLocator->get('EventDispatcher');
		$areaManager = $serviceLocator->get('AreaManager');

		// Get the active area
		if(($area = $areaManager->getActiveArea()) === null)
		{
			if($areaManager->getAreaStrategy() !== null)
			{
				$area = $areaManager->discoverActiveArea();
			}
			else
			{
				throw new Exception('No active area is selected.');
			}
		}
		$area->updateMetadata();

		// Get the active module
		$request = $serviceLocator->get('Request');
		$response = $serviceLocator->get('Response');
		$areaManager->setActiveModule($request->getParam('module', $area->defaultModule));
		$module = $areaManager->getActiveModule();

		$eventDispatcher->notify(new Event($this, 'web.application.modules-discovered', array('module' => $module, 'area' => $area)));

		// Get the controller
		$controller = $serviceLocator->get($area->controllerService);
		if($controller instanceof Controller)
		{
			$controller->dispatch($request, $response);
		}
		else
		{
			throw new Exception('The selected area controller is not a valid controller instance.');
		}
		$response->sendResponse();
	} // end launch();
} // end Application;