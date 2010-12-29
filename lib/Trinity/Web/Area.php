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
use \Symfony\Component\EventDispatcher\Event;
use \Trinity\Basement\Module;

/**
 * Abstract area class provides the minimal interface for communicating
 * with areas.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
abstract class Area extends Module
{
	/**
	 * The area metadata.
	 * @var array
	 */
	protected $_metadata = array();

	/**
	 * The list of verified metadata.
	 * @var array
	 */
	protected $_verifiedMetadata = array();

	/**
	 * This method should return the unique area name. The name will be also
	 * used to define sub-namespaces within the interested modules.
	 *
	 * @return string
	 */
	abstract public function getAreaName();

	/**
	 * The default launch method registers the area as active.
	 */
	public function launch()
	{
		$serviceLocator = $this->getServiceLocator();
		$areaManager = $serviceLocator->get('AreaManager');

		// The application should be always registered as the main area module.
		// This will simplify the code later.
		$areaManager->registerModuleForArea($serviceLocator->get('Application'), 'main', $this->getAreaName());

		// Now we select the active area.
		$areaManager->setActiveArea($this);
	} // end launch();

	/**
	 * This method is executed before dispatching the controller. The programmer
	 * may use it for any purpose.
	 */
	public function initArea()
	{

	} // end initArea();

	/**
	 * Sets the metadata for the area.
	 * @param array $data The metadata.
	 */
	public function setMetadata(array $data)
	{
		$this->_metadata = $data;
	} // end setMetadata();

	/**
	 * Returns the metadata set.
	 * @return array
	 */
	public function getMetadata()
	{
		return $this->_metadata;
	} // end getMetadata();

	/**
	 * Updates the area metadata, so that they can refer to the project
	 * configuration. This step must be provided as a separate method,
	 * because it must be fired after all the service containers are loaded.
	 * Otherwise, the area would not be able to refer to some of the options
	 * or use invalid values.
	 */
	public function updateMetadata()
	{
		$configuration = $this->getServiceLocator()->getConfiguration();
		foreach($this->_metadata as &$value)
		{
			if(is_scalar($value))
			{
				if(preg_match('/%([a-zA-Z0-9\-\.]+)%/', $value, $matches))
				{
					$value = str_replace($matches[0], $configuration->get($matches[1]), $value);
				}
			}
			else
			{
				foreach($value as &$subvalue)
				{
					if(preg_match('/%([a-zA-Z0-9\-\.]+)%/', $subvalue, $matches))
					{
						$subvalue = str_replace($matches[0], $configuration->get($matches[1]), $subvalue);
					}
				}
			}
		}
	} // end updateMetadata();

	/**
	 * Returns the metadata option. If the option does not exist, the method attempts
	 * to load it from the global configuration.
	 *
	 * @throws \Trinity\Basement\Exception
	 * @param name $name The option name.
	 * @return mixed
	 */
	public function __get($name)
	{
		if(!isset($this->_metadata[$name]))
		{
			$this->_metadata[$name] = $this->getServiceLocator()->getConfiguration()->get('application.area.default.'.$name);
		}
		return $this->_metadata[$name];
	} // end __get();

	public function __isset($name)
	{
		if(!isset($this->_metadata[$name]))
		{
			if(!isset($this->_verifiedMetadata[$name]))
			{
				$this->_verifiedMetadata[$name] = true;
				$config = $this->getServiceLocator()->getConfiguration();
				if($config->isDefined('application.area.default.'.$name))
				{
					$this->_metadata[$name] = $config->get('application.area.default.'.$name);
					return true;
				}
			}
			return false;
		}
		return true;
	} // end __isset();

	public function get($name)
	{
		if(!isset($this->_metadata[$name]))
		{
			$this->_metadata[$name] = $this->getServiceLocator()->getConfiguration()->get('application.area.default.'.$name);
		}
		return $this->_metadata[$name];
	} // end get();

	public function has($name)
	{
		if(!isset($this->_metadata[$name]))
		{
			if(!isset($this->_verifiedMetadata[$name]))
			{
				$this->_verifiedMetadata[$name] = true;
				$config = $this->getServiceLocator()->getConfiguration();
				if($config->isDefined('application.area.default.'.$name))
				{
					$this->_metadata[$name] = $config->get('application.area.default.'.$name);
					return true;
				}
			}
			return false;
		}
		return true;
	} // end has();
} // end Area;
