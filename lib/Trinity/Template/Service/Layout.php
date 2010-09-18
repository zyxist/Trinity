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
namespace Trinity\Template\Service;
use \Trinity\Basement\Service as Basement_Service;
use \Trinity\Template\Layout as Template_Layout;

/**
 * Launches the layout manager for views.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
class Layout extends Basement_Service
{

	/**
	 * List of services to preload.
	 * @return array
	 */
	public function toPreload()
	{
		return array('web.Broker', 'template.Opt');
	} // end toPreload();

	/**
	 * Builds the layout object.
	 */
	public function getObject()
	{
		$application = \Trinity\Basement\Application::getApplication();
		$broker = $this->_serviceLocator->get('web.Broker');
		$layout = new Template_Layout($application);
		$layout->setLayout($this->layout);

		// TODO: Replace with something more clever.
		$response = $broker->getResponse();
		$response->setHeader('Content-type', 'text/html;charset=utf-8');

		return $layout;
	} // end getObject();
} // end Layout;