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

namespace Trinity\Basement;
use Trinity\Basement\Service_Builder as Service_Builder;

/**
 * The standard service builder.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 * @todo It is not finished.
 */
class Service_Builder_Standard implements Service_Builder
{
	/**
	 * The list of services supported by this builder.
	 * @var array
	 */
	private $_services = array();

	/**
	 * Creates a new service builder.
	 *
	 * @param string $configFile The service configuration file.
	 */
	public function __construct($configFile)
	{
		$this->loadConfig($configFile);
	} // end __construct();

	/**
	 * Loads the service build configuration from an INI file. If the file
	 * contains errors, the method throws an exception.
	 *
	 * @throws Service_Builder_Exception
	 * @param string $configFile The path to the configuration file.
	 */
	public function loadConfig($configFile)
	{
		if(!file_exists($configFile))
		{
			throw new Service_Builder_Exception('The service definition file '.$configFile.' is not accessible.');
		}

		$opts = parse_ini_file($configFile, true);

		foreach($opts as $service => $serviceBuild)
		{
			// Check the service arguments
			if(!isset($serviceBuild['className']))
			{
				throw new Service_Builder_Exception('The required className attribute is not defined in '.$service.' defined in '.$configFile);
			}
			$output = array(
				'className' => $serviceBuild['className'],
				'constructor' => array(),
				'setter' => array()
			);
			if(!isset($serviceBuild['preload']))
			{
				$output['preload'] = array();
			}
			else
			{
				$output['preload'] = array_map('\trim', explode(',', $serviceBuild['preload']));
			}
			if(!isset($serviceBuild['postload']))
			{
				$output['postload'] = array();
			}
			else
			{
				$output['postload'] = array_map('\trim', explode(',', $serviceBuild['postload']));
			}

			// Parse the building rules.
			foreach($serviceBuild as $name => $value)
			{
				if(strpos($name, 'init.') === 0)
				{
					$name = explode('.', $name);
					if(sizeof($name) != 3)
					{
						throw new Service_Builder_Exception('Invalid option: '.$name.' in '.$service.' defined in '.$configFile);
					}
					switch($name[1])
					{
						case 'constructor':
							$output['constructor'][$name[2]] = $value;
							break;
						case 'setter':
							$output['setter'][$name[2]] = $value;
							break;
						default:
							throw new Service_Builder_Exception('Invalid option: '.$name.' in '.$service.' defined in '.$configFile);
					}
				}
			}

			$this->_services[$service] = $serviceBuild;
		}
	} // end loadConfig();

	/**
	 * Tests if the builder is able to build the specified service.
	 *
	 * @param string $name Service name
	 * @return boolean
	 */
	public function canBuild($name)
	{
		return isset($this->_services[$name]);
	} // end canBuild();

	/**
	 * Builds the service for the specified key using the stored rules.
	 *
	 * @throws Service_Builder_Exception
	 * @param string $name The name of the service to build.
	 * @param Locator_Service $serviceLocator The service locator that requests the service.
	 * @return Service
	 */
	public function build($name, Locator_Service $serviceLocator)
	{
		if(!isset($this->_services[$name]))
		{
			throw new Service_Builder_Exception('Cannot build the specified service: '.$name);
		}

		return new Service_Standard($serviceLocator, $this->_services[$name]);
	} // end build();
} // end Service_Builder_Standard;