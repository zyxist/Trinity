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
namespace Trinity\WebUtils\Service;
use \Trinity\Basement\Application as BaseApplication;
use \Trinity\Basement\Service as Basement_Service;
use \Trinity\Basement\Module;
use \Trinity\Web\View\Broker as View_Broker;
use \Trinity\WebUtils\Controller\Group as WebUtils_Controller_Group;

/**
 * The controller builder.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
class Controller_Group extends Basement_Service
{
	/**
	 * List of the required dependencies.
	 * @return array
	 */
	public function toPreload()
	{
		return array('web.Router', 'web.Area', 'web.Session', 'model.ModelLocator', 'template.HelperLocator', 'template.Opf', 'utils.Opc');
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

		$controller = new WebUtils_Controller_Group(BaseApplication::getApplication());

		$controller->setGroupModule($area->getAreaModule()->getSubmodule('Group'));
		$controller->setDefaults($this->defaultGroup, $this->defaultAction);
		$controller->setModelLocator($this->_serviceLocator->get('model.ModelLocator'));
		$controller->setHelperLocator($this->_serviceLocator->get('template.HelperLocator'));

		$controller->dispatch($broker->getRequest(), $broker->getResponse());
		return $controller;
	} // end getObject();
} // end Controller_Group;