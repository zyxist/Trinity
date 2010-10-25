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
namespace Trinity\Navigation\Service;
use \Trinity\Basement\Service as Basement_Service;
use \Trinity\Basement\Application as BaseApplication;
use \Trinity\Navigation\Manager as Navigation_Manager;

/**
 * Returns the navigation manager.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
class Manager extends Basement_Service
{
	/**
	 * The name of the reader service used to load the manager data.
	 * @var string
	 */
	protected $_readerSerivceName;

	/**
	 * Orders preloading the reader object.
	 *
	 * @return array
	 */
	public function toPreload()
	{
		$this->_readerSerivceName = $this->readerService;
		if($this->_readerSerivceName === null)
		{
			$this->_readerSerivceName = 'navigation.Reader\Php';
		}
		return array($this->_readerSerivceName, 'web.Broker', 'web.Controller');
	} // end toPreload();

	/**
	 * Preconfigures and initializes the configuration object.
	 *
	 * @return \Trinity\Navigation\Manager
	 */
	public function getObject()
	{
		$application = \Trinity\Basement\Application::getApplication();
		$eventDispatcher = $application->getEventDispatcher();

		$manager = new Navigation_Manager;
		$manager->setReader($application->getServiceLocator()->get($this->_readerServiceName));

		$eventDispatcher->notify(new Event($manager, 'navigation.manager.init.hooks'));

		$manager->discover();

		// OK, now register an event for the controller.
		$controller = $this->_serviceLocator->get('web.Controller');
		$broker = $this->_serviceLocator->get('web.Broker');

		$meaningfulArguments = $this->meaningfulArguments;
		if($meaningfulArguments === null)
		{
			$meaningfulArguments = array('id');
		}
		else
		{
			$meaningfulArguments = explode(',', $meaningfulArguments);
		}

		// This hook will be executed before the controller dispatching, so that
		// it can use the controller and request data to find the active page.
		$eventDispatcher->addCallback('controller.'.$controller->getName().'.dispatch', function(Event $event) use($controller, $manager, $broker, $meaningfulArguments) {
			$activePageInfo = array();
			$request = $broker->getRequest();
			foreach($event as $name => $value)
			{
				if(is_scalar($value))
				{
					$activePageInfo[$name] = $value;
				}
			}
			foreach($meaningfulArguments as $name)
			{
				$name = trim($name);
				if($request->hasParam($name))
				{
					$activePageInfo[$name] = $request->getParam($name);
				}
			}
			$manager->findActivePage($controller->getName(), $activePageInfo);
		});

		return $manager;
	} // end getObject();
} // end Manager;
