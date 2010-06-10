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
 *
 * $Id$
 */
namespace Trinity\Garage;
use \Trinity\Garage\Config\Loader_Ini as Loader_Ini;
use \Trinity\Basement\Service as Service;

/**
 * The configuration service
 */
class Service_Config extends Service
{
	/**
	 * Preconfigures and initializes the configuration object.
	 *
	 * @return Config
	 */
	public function getObject()
	{
		$config = new Config;
		$config->setEnvironment($this->environment);
		$config->loadConfig(new Loader_Ini($this->configPath));

		return $config;
	} // end getObject();
} // end Service_Config;