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
use \Opc\Visit;
use \Symfony\Component\EventDispatcher\Event;
use \Symfony\Component\EventDispatcher\EventDispatcher;
use \Trinity\Basement\Service\Container;
use \Trinity\Basement\ServiceLocator;
use \Trinity\Basement\ObjectLocator;
use \Trinity\Web\Area\Manager as Area_Manager;
use \Trinity\Web\Router\Standard as Router_Standard;
use \Trinity\Web\View\Broker as View_Broker;

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
			'trinity.web.response.default-content-type' => 'text/html',
			'trinity.web.response.default-charset' => 'utf-8',
			'trinity.web.session.handler-service' => null,
			'trinity.web.router.route-file' => 'config/routes.php',
			'trinity.web.areaManager.modules-tied-to-areas' => true,
			'trinity.web.areaManager.strategy-service' => null,
			'trinity.web.router.query-path' => '/',
			'trinity.web.router.base-url' => null
		);
	} // end getConfiguration();

	/**
	 * The service for building the event dispatcher.
	 *
	 * @param ServiceLocator $serviceLocator The service locator.
	 * @return EventDispatcher 
	 */
	public function getEventDispatcherService(ServiceLocator $serviceLocator)
	{
		return new EventDispatcher;
	} // end getEventDispatcherService();

	/**
	 * The service for building the OPC visit object.
	 *
	 * @param ServiceLocator $serviceLocator The service locator.
	 * @return Opc_Visit
	 */
	public function getVisitService(ServiceLocator $serviceLocator)
	{
		return new Visit;
	} // end getVisitService();

	/**
	 * Creates the request object.
	 *
	 * @param ServiceLocator $serviceLocator The service locator.
	 * @return Request
	 */
	public function getRequestService(ServiceLocator $serviceLocator)
	{
		$request = new Request\Http($serviceLocator->get('Visit'));
		$router = $serviceLocator->get('Router');
		$request->setParams($router->route($request->pathInfo));

		return $request;
	} // end getRequestService();

	/**
	 * Creates the response object.
	 *
	 * @param ServiceLocator $serviceLocator The service locator.
	 * @return Response
	 */
	public function getResponseService(ServiceLocator $serviceLocator)
	{
		$args = $serviceLocator->getConfiguration();
		$response = new Response;

		// The default charset.
		$response->setHeader('Content-type', $args->get('trinity.web.response.default-content-type').';charset='.$args->get('trinity.web.response.default-charset'));

		// Connect some events
		$eventDispatcher = $serviceLocator->get('EventDispatcher');
		$eventDispatcher->connect('controller.web.dispatch.end', function(Event $event) use($response){
			$manager = $event->getParameter('manager');
			$viewBroker = $manager->getViewBroker();
		//	$viewBroker->setRequest($args['manager']->request);
			$viewBroker->setResponse($manager->response);
			if($viewBroker instanceof View_Broker)
			{
				$viewBroker->display();
			}
		}, 10);

		return $response;
	} // end getResponseService();

	/**
	 * Constructs the session service.
	 *
	 * @param ServiceLocator $serviceLocator The service locator.
	 * @return Session
	 */
	public function getSessionService(ServiceLocator $serviceLocator)
	{
		$args = $serviceLocator->getConfiguration();
		$session = new Session($serviceLocator->get('EventDispatcher'));

		if($args->get('trinity.web.session.handler-service') !== null)
		{
			$session->setSessionHandler($serviceLocator->get($args->get('trinity.web.session.handler-service')));
		}

		return $session;
	} // end getSessionService();

	/**
	 * Creates and initializes the router.
	 * 
	 * @param ServiceLocator $serviceLocator The service locator.
	 * @return Router
	 */
	public function getRouterService(ServiceLocator $serviceLocator)
	{
		$args = $serviceLocator->getConfiguration();
		$application = $serviceLocator->get('Application');
		$router = new Router_Standard($args->get('trinity.web.router.query-path'), $args->get('trinity.web.router.base-url'));

		require($application->getDirectory().$args->get('trinity.web.router.route-file'));

		return $router;
	} // end getRouterService();

	/**
	 * Creates the area manager service.
	 * 
	 * @param ServiceLocator $serviceLocator
	 * @return AreaManager
	 */
	public function getAreaManagerService(ServiceLocator $serviceLocator)
	{
		$args = $serviceLocator->getConfiguration();
		$areaManager = new Area_Manager;
		$areaManager->setModulesTiedToAreas($args->get('trinity.web.areaManager.modules-tied-to-areas'));

		if($args->get('trinity.web.areaManager.strategy-service') !== null)
		{
			$areaManager->setAreaStrategy($serviceLocator->get($args->get('trinity.web.areaManager.strategy-service')));
		}

		return $areaManager;
	} // end getAreaManagerService();

	/**
	 * Creates the model locator service.
	 *
	 * @param ServiceLocator $serviceLocator The service locator.
	 * @return ObjectLocator 
	 */
	public function getModelLocatorService(ServiceLocator $serviceLocator)
	{
		return new ObjectLocator(
			'modelLocator',
			function($name) use ($serviceLocator){
				$className = str_replace('.', '\\', $name);

				return new $className($serviceLocator);
			}
		);
	} // end getModelLocatorService();
} // end Services;