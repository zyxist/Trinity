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
use \Trinity\Basement\Service_Configurator as Service_Configurator;
use \Trinity\Utils\Config\Loader as Loader;

/**
 * The configuration manager.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
class Config implements Service_Configurator
{
	/**
	 * The loaded configuration options.
	 *
	 * @var array
	 */
	private $_options = array();

	/**
	 * The environment name.
	 *
	 * @var string
	 */
	private $_environment;

	/**
	 * Returns the name of the current environment.
	 *
	 * @return string The environment name.
	 */
	public function getEnvironment()
	{
		return $this->_environment;
	} // end getEnvironment();

	/**
	 * Sets the name of the current environment.
	 *
	 * @param string $environment The environment name.
	 */
	public function setEnvironment($environment)
	{
		$this->_environment = $environment;
	} // end setEnvironment();

	/**
	 * Loads the configuration from the specified source.
	 *
	 * @param Loader $loader The configuration loader
	 */
	public function loadConfig(Loader $loader)
	{
		$options = $loader->loadConfig($this->_environment);

		if($loader->getOutputType() == Loader::PLAIN_LIST)
		{
			// TODO: Optimize that.
			foreach($options as $name => $opt)
			{
				$ns = \explode('.', $name);
				$aggregator = $this;
				$lastIdx = sizeof($ns) - 1;
				foreach($ns as $id => $item)
				{
					if($id == $lastIdx)
					{
						$aggregator->_options[$item] = $opt;
					}
					elseif(!isset($aggregator->_options[$item]) || !$aggregator->_options[$item] instanceof Config)
					{
						$aggregator = $aggregator->_options[$item] = new Config;
					}
					else
					{
						$aggregator = $aggregator->_options[$item];
					}
				}
			}
		}
		else
		{
			// TODO: Implement that
		}
	} // end loadConfig();

	/**
	 * The getter for retrieving the configuration options.
	 *
	 * @throws Config_Exception
	 * @param string $name The option name.
	 */
	public function __get($name)
	{
		if(!isset($this->_options[$name]))
		{
			throw new Config_Exception('Such an option: '.$name.' does not exist.');
		}
		return $this->_options[$name];
	} // end __get();

	/**
	 * Checks if the specified option exists.
	 *
	 * @param string $name The option name.
	 */	
	public function __isset($name)
	{
		return isset($this->_options[$name]);
	} // end __isset();

	/**
	 * Returns an array with all the options.
	 *
	 * @return array
	 */
	public function toArray()
	{
		return $this->_options;
	} // end toArray();

	/**
	 * Returns the service options.
	 * 
	 * @param string $name The service name.
	 */
	public function getServiceOptions($name)
	{
		if(!isset($this->_options['services']) || !$this->_options['services'] instanceof Config)
		{
			return array();
		}
		$path = explode('.', $name);

		$whereTo = $this->_options['services'];
		foreach($path as $item)
		{
			if(!isset($whereTo->{$item}))
			{
				return array();
			}
			$whereTo = $whereTo->__get($item);
			if(!$whereTo instanceof Config)
			{
				return array();
			}
		}
		return $whereTo->toArray();
	} // end getServiceOptions();
} // end Config;