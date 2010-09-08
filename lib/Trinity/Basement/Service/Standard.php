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
use Trinity\Basement\Service as Service;
use Trinity\Basement\Locator_Service as Locator_Service;
use Trinity\Basement\Service_Builder_Exception as Service_Builder_Exception;
use Trinity\Basement\Application as Application;

/**
 * The standard service representation used by the standard
 * service builder. This is not a production-ready interface,
 * but rather a bit dummy thing for now.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
class Service_Standard extends Service
{
	private $_rules;
	
	public function __construct(Locator_Service $serviceLocator, array $rules)
	{
		parent::__construct($serviceLocator);
		$this->_rules = $rules;
	} // end __construct();

	public function toPreload()
	{
		return $this->_rules['preload'];
	} // end toPreload();

	public function toPostload()
	{
		return $this->_rules['postload'];
	} // end toPostload();

	public function getObject()
	{
		// Parse the constructor arguments
		$args = array();
		foreach($this->_rules['constructor'] as $name => $value)
		{
			$args[] = $this->_translateValue($value);
		}

		// Create the object
		$reflected = new \ReflectionClass($this->_rules['className']);

		$service = call_user_func_array(array($this->_rules['className'], $reflected->getConstructor()), $args);

		// Initialize the object via setters

		foreach($this->_rules['setter'] as $methodName => $argument)
		{
			if(!$reflected->hasMethod($methodName))
			{
				throw new Service_Builder_Exception('The setter '.$methodName.' is not defined in '.$this->_rules['className']);
			}
			call_user_func(array($service, $methodName), $this->_translateValue($argument));
		}

		// Return the complete object.
		return $service;
	} // end getObject();

	private function _translateValue($value)
	{
		if(!preg_match('/^(service|option|system|value|call)\:(.*)$/', $value, $matches))
		{
			throw new Service_Builder_Exception('Invalid value definition: '.$value);
		}

		switch($matches[1])
		{
			case 'service':
				return $this->_serviceLocator->get($matches[1]);
				break;
			case 'option':
				return $this->__get($matches[1]);
			case 'system':
				switch($matches[2])
				{
					case 'application':
						return Application::getApplication();
					case 'eventDispatcher':
						return Application::getApplication()->getEventDispatcher();
					case 'serviceLocator':
						return $this->_serviceLocator;
				}
		}
		return null;
	} // end _translateValue();
} // end Service_Standard;