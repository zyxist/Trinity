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
namespace Trinity\Web\Service;
use \Trinity\Basement\Service as Basement_Service;
use \Trinity\Web\Area as Web_Area;
use \Trinity\Web\Area\Strategy\File as Strategy_File;

/**
 * The area selector.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
class Area extends Basement_Service
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
		return array('web.AreaStrategy', 'utils.Config', 'web.Visit', 'web.Broker');
	} // end toPreload();

	/**
	 * Preconfigures and initializes the configuration object.
	 *
	 * @return Area
	 */
	public function getObject()
	{
		$application = \Trinity\Basement\Application::getApplication();
		$request = $this->_serviceLocator->get('web.Broker')->getRequest();
		$moduleManager = $application->getModuleManager();

		// Initialize the area
		$area = new Web_Area($application, $this->_serviceLocator->get('web.AreaStrategy'));
		$area->setPrimaryModule($module = $moduleManager->getModule($request->getParam('module', $this->defaultModule)));
		$area->setAreaModule($module->getSubmodule($area->getName()));
		$request->setArea($area);

		// Get the controller name
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
} // end Area;