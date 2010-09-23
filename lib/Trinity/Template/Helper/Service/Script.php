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
use \Trinity\Template\Helper\Script as Helper_Script;
use \Opt_View;

/**
 * Launches the javascript helper.
 *
 * @author Amadeusz "megawebmaster" Starzykiewicz
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
class Script extends Basement_Service
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
		$script = new Helper_Script();
		$script->setBaseUrl($config->baseUrl);

		Opt_View::assignGlobal(
			'helper',
			array_merge(
				Opt_View::definedGlobal('helper')?Opt_View::getGlobal('helper'):array(),
				array('script' => $script)
			)
		);
		Opt_View::setFormatGlobal('helper', 'Global/Array', false);
		Opt_View::setFormatGlobal('script', 'Helper', false);

		return $script;
	} // end getObject();
} // end Script;