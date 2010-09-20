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
namespace Trinity\Utils;
use \Trinity\Basement\Service as Service;

/**
 * The configuration service
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
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
		$loader = '\Trinity\Utils\Config\Loader_'.$this->configLoader;
		$config->loadConfig(new $loader($this->configPath));

		$this->_serviceLocator->addConfigurator('config', $config);
		$this->_serviceLocator->setDefaultConfigurator('config');

		return $config;
	} // end getObject();
} // end Service_Config;