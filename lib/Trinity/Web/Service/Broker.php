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
use \Trinity\Basement\Service as Service;

/**
 * The broker selector.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
class Service_Broker extends Service
{
	/**
	 * List of services to preload.
	 * @return array
	 */
	public function toPreload()
	{
		return array('web.Visit', 'web.Router');
	} // end toPreload();

	/**
	 * Preconfigures and initializes the configuration object.
	 *
	 * @return Broker_Abstract
	 */
	public function getObject()
	{
		$application = \Trinity\Basement\Application::getApplication();
		// Initialize the broker
		$broker = new Broker_Standard($application);
		$broker->buildRequest($this->_serviceLocator->get('web.Visit'));
		$broker->buildResponse();

		// Connect some broker events
		$application->getEventManager()->addCallback('controller.web.dispatch.end', function($args) use($broker){
			$viewBroker = $args['controller']->getViewBroker();
			$viewBroker->setRequest($args['request']);
			$viewBroker->setResponse($args['response']);
			if($viewBroker instanceof View_Broker)
			{
				$viewBroker->display();
			}
			$args['response']->sendResponse();
		});
		$application->getEventManager()->addCallback('controller.web.dispatch.redirect', function($args){
			$args['response']->sendResponse();
		});

		return $broker;
	} // end getObject();
} // end Service_Config;