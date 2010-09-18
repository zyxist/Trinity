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
use \Trinity\Basement\Application as Base_Application;
use \Trinity\Basement\Service_Configurator as Service_Configurator;
use \Trinity\Basement\Service_Builder_Standard as Service_Builder_Standard;
use \Trinity\Basement\Module\Manager as Module_Manager;

/**
 * An interface for web applications.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
class Application extends Base_Application implements Service_Configurator
{
	/**
	 * The application environment.
	 * @var string
	 */
	private $_environment;

	/**
	 * The path to the configuration file.
	 * @var string
	 */
	private $_configPath;

	/**
	 * Initializes a web application.
	 *
	 * @param string $appName The application name
	 * @param string $environment The environment name to use
	 * @param string $configPath The path to the configuration
	 * @param string $modulePath The path to modules.
	 */
	public function __construct($appName, $environment, $configPath, $modulePath)
	{
		$this->_environment = $environment;
		$this->_configPath = $configPath;
		$this->setModuleManager(new Module_Manager($appName, $modulePath));
	} // end __construct();

	/**
	 * Returns the service options for utils.Config.
	 *
	 * @param string $serviceName The service name
	 * @return array
	 */
	public function getServiceOptions($serviceName)
	{
		if($serviceName == 'utils.Config')
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
		$locator->addServiceGroup('template', '\Trinity\Template\Service_');
		$locator->addServiceGroup('web', '\Trinity\Web\Service\\');
		$locator->addServiceGroup('webUtils', '\Trinity\WebUtils\Service_');

		$locator->addConfigurator('application', $this);
		$locator->setDefaultConfigurator('application');
	//	$locator->setServiceBuilder(new Service_Builder_Standard($this->_servicePath));

		// Ensure that the configuration will always be loaded in the first place in
		// order to configure it as a new configurator.
		$this->getServiceLocator()->get('utils.Config');

		// Load the application module.
		$module = $this->getModuleManager()->getModule('');

		// Now you can do the rest.
		$obj = $this->getServiceLocator()->get('web.Area');
	} // end _launch();
} // end Application;