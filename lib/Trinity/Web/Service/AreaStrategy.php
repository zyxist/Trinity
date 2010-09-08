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
use \Trinity\Web\Area\Strategy\File as Strategy_File;

/**
 * Initializes the area selection strategy.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
class Service_AreaStrategy extends Service
{
	/**
	 * List of services to preload.
	 * @return array
	 */
	public function toPreload()
	{
		return array('utils.Config', 'web.Visit');
	} // end toPreload();

	/**
	 * Preconfigures and initializes the configuration object.
	 *
	 * @return Area_Abstract
	 */
	public function getObject()
	{
		// Initialize the area discovery strategy
		$strategy = new Strategy_File($this->areaList);
		$strategy->setDiscoveryType($this->discoveryType, $this->_serviceLocator->get('web.Visit'));
		if($this->defaultArea !== null)
		{
			$strategy->setDefaultArea($this->defaultArea);
		}

		// Connect to the view helpers.
		\Trinity\Template\Helper_Url::setStrategy($strategy);

		return $strategy;
	} // end getObject();
} // end Service_AreaStrategy;