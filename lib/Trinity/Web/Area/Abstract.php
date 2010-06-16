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
use \Trinity\Basement\Application as BaseApplication;
use \Trinity\Basement\Module as Module;

/**
 * Abstract area class provides the minimal interface for communicating
 * with areas.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
abstract class Area_Abstract extends Module
{
	/**
	 * The application link.
	 * @var BaseApplication
	 */
	protected $_application;

	/**
	 * The area name.
	 * @var string
	 */
	protected $_name = 'Default';

	/**
	 * The area options.
	 * @var array
	 */
	protected $_options = array();

	/**
	 * Constructs the area object.
	 * 
	 * @param Application $application The application object
	 */
	public function __construct(BaseApplication $application)
	{
		$this->_application = $application;
	} // end __construct();

	/**
	 * Returns the area name.
	 *
	 * @return string
	 */
	public function getName()
	{
		return $this->_name;
	} // end getName();

	/**
	 * Returns the area options.
	 *
	 * @return array
	 */
	public function getOptions()
	{
		return $this->_options;
	} // end getOptions();

	/**
	 * Returns the area option value. If the option is not defined, an
	 * exception is thrown.
	 *
	 * @throws Area_Exception.
	 * @param string $name Option name
	 * @return mixed
	 */
	public function getOption($name)
	{
		if(!isset($this->_options[$name]))
		{
			throw new Area_Exception('The specified area option '.$name.' does not exist.');
		}
		return $this->_options[$name];
	} // end getOption();

	/**
	 * Returns the area option value. If the option is not defined, a
	 * default value is returned.
	 * 
	 * @param string $name Option name
	 * @param mixed $default Default option value
	 * @return mixed
	 */
	public function getOptionDef($name, $default = null)
	{
		if(!isset($this->_options[$name]))
		{
			return $default;
		}
		return $this->_options[$name];
	} // end getOptionDef();

	/**
	 * Checks if the area has the specified option.
	 *
	 * @param string $name The option name
	 * @return boolean
	 */
	public function hasOption($name)
	{
		return isset($this->_options[$name]);
	} // end hasOption();

	/**
	 * Returns the name of the controller service to use in this area.
	 *
	 * @return string
	 */
	abstract public function getController();
} // end Area_Abstract;