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
			'trinity.web.cache.class' => '\Trinity\Cache\APC',
			'trinity.web.cache.options' => array(
				'prefix' => 'trinity',
				'lifetime' => 86400
			),
			'trinity.web.request.kept-args' => 'module,group,action,id',
			'trinity.web.response.default-content-type' => 'text/html',
			'trinity.web.response.default-charset' => 'utf-8',
			'trinity.web.session.handler-service' => null,
			'trinity.web.router.route-file' => '%application.directory%config/routes.php',
			'trinity.web.areaManager.modules-tied-to-areas' => true,
			'trinity.web.areaManager.strategy-service' => null,
			'trinity.web.areaManager.metadata-service' => 'FileMetadataLoader',
			'trinity.web.fileMetadataLoader.class' => '\Trinity\Web\Area\MetadataLoader\XmlLoader',
			'trinity.web.fileMetadataLoader.paths' => '%application.directory%config/',
			'trinity.web.fileMetadataLoader.file' => 'area.xml',
			'trinity.web.router.query-path' => '/',
			'trinity.web.router.base-url' => null,
			'trinity.opc.paginator.decorator' => 'slider'
		);
	} // end getConfiguration();

	/**
	 * Builds the caching service.
	 * 
	 * @param ServiceLocator $serviceLocator
	 * @return \Trinity\Cache\Cache
	 */
	public function getCacheService(ServiceLocator $serviceLocator)
	{
		$args = $serviceLocator->getConfiguration();
		$className = $args->get('trinity.web.cache.class');

		$object = new $className($args->get('trinity.web.cache.options'));
		if(!$object instanceof \Trinity\Cache\Cache)
		{
			throw new Exception('Invalid cache class: '.get_class($object));
		}
	//	$object->clean(\Trinity\Cache\Cache::ALL);
		return $object;
	} // end getCacheService();

	/**
	 * Builds the area metadata loader service.
	 *
	 * @param ServiceLocator $serviceLocator
	 * @return \Trinity\Web\Area\MetadataLoader\FileLoader
	 */
	public function getFileMetadataLoaderService(ServiceLocator $serviceLocator)
	{
		$args = $serviceLocator->getConfiguration();
		$className = $args->get('trinity.web.fileMetadataLoader.class');

		$object = new $className($args->get('trinity.web.fileMetadataLoader.paths'));
		if(!$object instanceof \Trinity\Web\Area\MetadataLoader\FileLoader)
		{
			throw new Exception('Invalid area file metadata loader class: '.get_class($object));
		}
		$object->setFile($args->get('trinity.web.fileMetadataLoader.file'));
		return $object;
	} // end getFileMetadataLoaderService();

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
	 * The service for building the OPC core object.
	 *
	 * @param ServiceLocator $serviceLocator The service locator.
	 * @return Opc\Core
	 */
	public function getOpcService(ServiceLocator $serviceLocator)
	{
		$config = $serviceLocator->getConfiguration();
		$core = new \Opc\Core();
		$core->paginatorDecorator = $config->get('trinity.opc.paginator.decorator');

		return $core;
	} // end getOpcService();

	/**
	 * The service for building the OPC visit object.
	 *
	 * @param ServiceLocator $serviceLocator The service locator.
	 * @return Opc\Visit
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
		$args = $serviceLocator->getConfiguration();
		$request = new Request\Http($serviceLocator->get('Visit'));

		$router = $serviceLocator->get('Router');
		$request->setParams($router->route($request->pathInfo));

		$keptArgs = explode(',', $args->get('trinity.web.request.kept-args'));
		foreach($keptArgs as $argument)
		{
			if($request->hasParam($argument))
			{
				$router->setParam($argument, $request->getParam($argument));
			}
		}

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

		$session->start();

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
		$router = new Router_Standard($serviceLocator->get('AreaManager'));

		require($args->get('trinity.web.router.route-file'));

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
		$areaManager = new Area_Manager($serviceLocator->get('Cache'), $serviceLocator->get($args->get('trinity.web.areaManager.metadata-service')));
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