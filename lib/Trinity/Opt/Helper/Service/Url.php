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
namespace Trinity\Template\Helper\Service;
use \Trinity\Basement\Service as Basement_Service;
use \Trinity\Template\Helper\Url as Helper_Url;
use \Opt_View;

/**
 * Launches the stylesheet helper.
 *
 * @author Amadeusz "megawebmaster" Starzykiewicz
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
class Url extends Basement_Service
{
	/**
	 * List of services to preload.
	 * @return array
	 */
	public function toPreload()
	{
		return array('utils.Config');
	} // end toPreload();

	/**
	 * Builds the layout object.
	 */
	public function getObject()
	{
		$config = $this->_serviceLocator->get('utils.Config');
		$url = new Helper_Url();

		// Configure helper
		$url->setBaseUrl($config->baseUrl);
		$url->setStrategy($this->_serviceLocator->get('web.AreaStrategy'));
		$url->setRouter($this->_serviceLocator->get('web.Router'));

		Opt_View::assignGlobal(
			'helper',
			array_merge(
				Opt_View::definedGlobal('helper')?Opt_View::getGlobal('helper'):array(),
				array('baseUrl' => $url->baseUrl())
			)
		);
		Opt_View::setFormatGlobal('helper', 'Global/Array', false);

		return $url;
	} // end getObject();
} // end Url;