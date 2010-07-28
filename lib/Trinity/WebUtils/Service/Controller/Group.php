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
use \Trinity\Basement\Application as BaseApplication;
use \Trinity\Basement\Service;
use \Trinity\Basement\Module;
use \Trinity\Web\View_Broker;

/**
 * The controller builder.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
class Service_Controller_Group extends Service
{
	/**
	 * List of the required dependencies.
	 * @return array
	 */
	public function toPreload()
	{
		return array('web.Router', 'web.Area', 'web.Session', 'model.ModelLocator', 'template.Opf', 'utils.Opc');
	} // end toPreload();

	/**
	 * Preconfigures and initializes the controller object.
	 *
	 * @return \Trinity\WebUtils\Controller\ActionGroup
	 */
	public function getObject()
	{
		$area = $this->_serviceLocator->get('web.Area');
		$broker = $this->_serviceLocator->get('web.Broker');

		$controller = new Controller\Group(BaseApplication::getApplication());

		$controller->setGroupModule($area->getAreaModule()->getSubmodule('Group'));
		$controller->setDefaults($this->defaultGroup, $this->defaultAction);
		$controller->setModelLocator($this->_serviceLocator->get('model.ModelLocator'));

		$controller->dispatch($broker->getRequest(), $broker->getResponse());
		return $controller;
	} // end getObject();
} // end Service_Controller_ActionGroup;