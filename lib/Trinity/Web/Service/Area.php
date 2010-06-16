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
 * The area selector.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
class Service_Area extends Service
{
	/**
	 * The controller name requested by the area.
	 * @var string
	 */
	private $_controllerName = null;

	/**
	 * List of services to preload.
	 * @return array
	 */
	public function toPreload()
	{
		return array('utils.Config', 'web.Visit', 'web.Broker');
	} // end toPreload();

	/**
	 * Preconfigures and initializes the configuration object.
	 *
	 * @return Area_Abstract
	 */
	public function getObject()
	{
		$application = \Trinity\Basement\Application::getApplication();
		$request = $this->_serviceLocator->get('web.Broker')->getRequest();
		// Initialize the area
		// TODO: Replace with Opc_Visit data!
		$area = new Area_File($application, $this->areaList, $_SERVER['SERVER_NAME']);
		$area->setModule($request->getParam('module', $this->defaultModule));
		$request->setArea($area);

		$this->_controllerName = $area->getController();

		return $area;
	} // end getObject();

	/**
	 * List of services to postload.
	 * @return array
	 */
	public function toPostload()
	{
		if($this->_controllerName !== null)
		{
			return array($this->_controllerName);
		}
		return array();
	} // end toPostload();
} // end Service_Config;