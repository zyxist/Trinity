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

namespace Trinity\Hall;
use Trinity\Basement\Application as Base_Application;
use Trinity\Basement\Service_Configurator as Service_Configurator;
use Trinity\Basement\Service_Builder_Standard as Service_Builder_Standard;

class Application extends Base_Application implements Service_Configurator
{
	private $_environment;

	private $_configPath;

	private $_servicePath;

	/**
	 * Initializes a web application.
	 * 
	 * @param string $environment The environment name to use
	 * @param string $configPath The path to the configuration
	 */
	public function __construct($environment, $configPath, $servicePath)
	{
		$this->_environment = $environment;
		$this->_configPath = $configPath;
		$this->_servicePath = $servicePath;
	} // end __construct();

	public function getServiceOptions($serviceName)
	{
		if($serviceName == 'garage.Config')
		{
			return array('environment' => $this->_environment, 'configPath' => $this->_configPath);
		}
		return array();
	} // end getServiceOptions();

	/**
	 * The launch procedure.
	 */
	protected function _launch()
	{
		$locator = $this->getServiceLocator();
		$locator->addConfigurator('application', $this);
		$locator->setDefaultConfigurator('application');
		$locator->setServiceBuilder(new Service_Builder_Standard($this->_servicePath));

		$obj = $this->getServiceLocator()->get('garage.Config');
	} // end _launch();
} // end Application;