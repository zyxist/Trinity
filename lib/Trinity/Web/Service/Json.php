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
namespace Trinity\Template;
use \Trinity\Basement\Service as Service;
use \Trinity\Web\View\Json;

/**
 * Launches the JSON view broker.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
class Service_Json extends Service
{

	/**
	 * List of services to preload.
	 * @return array
	 */
	public function toPreload()
	{
		return array('web.Broker');
	} // end toPreload();

	/**
	 * Builds the JSON object.
	 */
	public function getObject()
	{
		$application = \Trinity\Basement\Application::getApplication();
		$broker = $this->_serviceLocator->get('web.Broker');
		$json = new Json($application);



		return $json;
	} // end getObject();
} // end Service_Json;