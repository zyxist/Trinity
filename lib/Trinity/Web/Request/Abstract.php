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

/**
 * Represents a controller request.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
abstract class Request_Abstract
{
	/**
	 * The name of the module to execute.
	 * @var string
	 */
	private $_module;

	/**
	 * The request area
	 * @var \Trinity\Web\Area_Standard
	 */
	private $_area = null;

	/**
	 * The parameter list.
	 * @var array
	 */
	private $_params = array();

	/**
	 * Returns the name of the module.
	 *
	 * @return string
	 */
	public function getModuleName()
	{
		return $this->_module;
	} // end getModuleName();

	/**
	 * Sets the request module name.
	 * @param string $module The module name
	 */
	public function setModuleName($module)
	{
		$this->_module = (string)$module;
	} // end setModuleName();

	/**
	 * Returns the request area
	 *
	 * @return \Trinity\Web\Area_Standard
	 */
	public function getArea()
	{
		return $this->_area;
	} // end getArea();

	/**
	 * Sets the request area.
	 *
	 * @param \Trinity\Web\Area_Standard $area
	 */
	public function setArea(Area_Standard $area)
	{
		$this->_area = $area;
	} // end setArea();

	/**
	 * Returns the request parameters
	 */
	public function getParams()
	{
		return $this->_params;
	} // end getParams();

	/**
	 * Sets the list of request parameters from an array.
	 *
	 * @param array $params The list of request parameters
	 */
	public function setParams(array $params)
	{
		$this->_params = $params;
	} // end setParams();

	/**
	 * Returns the request parameter with the specified name. If the parameter
	 * is not set, a default value is returned.
	 *
	 * @param string $name The parameter name
	 * @param mixed $default The default value
	 * @return mixed The parameter value
	 */
	public function getParam($name, $default = null)
	{
		if(!isset($this->_params[$name]))
		{
			return $default;
		}
		return $this->_params[$name];
	} // end getParam();

	/**
	 * Returns true, if the specified parameter is set.
	 * @param string $name The parameter name
	 * @return boolean
	 */
	public function hasParam($name)
	{
		return isset($this->_params[(string)$name]);
	} // end hasParam();

	/**
	 * Sets the request parameter to the specified value.
	 * 
	 * @param string $name The parameter name
	 * @param mixed $value The new parameter value
	 */
	public function setParam($name, $value)
	{
		$this->_params[(string)$name] = $value;
	} // end setParam();

	/**
	 * Clears the parameter list.
	 */
	public function clearParams()
	{
		$this->_params = array();
	} // end clearParams();
} // end Request_Abstract;