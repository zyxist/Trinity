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
use \Trinity\Basement\Service as Service;
use \Trinity\Web\Router\Standard as Router_Standard;

/**
 * The routing service.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
class Service_Router extends Service
{
	/**
	 * List of services to preload.
	 * @return array
	 */
	public function toPreload()
	{
		return array('web.AreaStrategy', 'utils.Config');
	} // end toPreload();

	/**
	 * Preconfigures and initializes the configuration object.
	 *
	 * @return Config
	 */
	public function getObject()
	{
		$application = \Trinity\Basement\Application::getApplication();

		$config = $this->_serviceLocator->get('utils.Config');
		// Initialize the router
		$router = new Router_Standard($this->_serviceLocator->get('web.AreaStrategy'), $config->queryPath, $config->baseUrl);

		require($this->routes);

		// Connect the router to the broker.
		$application->getEventDispatcher()->connect('web.broker.request.create', function(Event $event) use($router)
		{
			$request = $event['request'];
			$request->setParams($router->route($request->pathInfo));
		});

		// Connect to the view helpers.
		\Trinity\Template\Helper_Url::setRouter($router);

		return $router;
	} // end getObject();
} // end Service_Config;