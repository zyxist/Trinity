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
use \Trinity\Basement\Module;
use \Trinity\Web\Area\Strategy;

/**
 * Abstract area class provides the minimal interface for communicating
 * with areas.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
class Area
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
	 * The area module
	 * @var Module
	 */
	private $_areaModule;

	/**
	 * The selected module.
	 * @var Module
	 */
	private $_primaryModule;

	/**
	 * Constructs the area object, discovering the selected area using the
	 * specified strategy. Note that strategy discovery process may throw
	 * an exception.
	 *
	 * @throws Area_Exception
	 * @param Application $application The application object
	 * @param Strategy $strategy Area discovering strategy
	 */
	public function __construct(BaseApplication $application, Strategy $strategy)
	{
		$this->_application = $application;

		list($name, $data) = $strategy->discoverArea();

		$this->_validateOptions($data);

		$this->_name = $name;
		$this->_options = $data;
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
			throw new Area\Exception('The specified area option '.$name.' does not exist.');
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
	 * Selects the primary module.
	 *
	 * @param \Trinity\Basement\Module $module The new primary module.
	 */
	public function setPrimaryModule(Module $module)
	{
		$this->_primaryModule = $module;
	} // end setModule();

	/**
	 * Selects the area module.
	 *
	 * @param \Trinity\Basement\Module $module The new area module.
	 */
	public function setAreaModule(Module $module)
	{
		$this->_areaModule = $module;
	} // end setAreaModule();

	/**
	 * Returns the path for the code within the given area.
	 *
	 * @param string $item Directory name
	 * @return string
	 */
	public function getCodePath($item)
	{
		if($this->_areaModule !== null)
		{
			return $this->_areaModule->getCodePath($item);
		}
		return $this->_primaryModule->getCodePath($item);
	} // end getCodePath();

	/**
	 * Returns the path for the data item within the given area.
	 *
	 * @param string $item Directory name
	 * @return string
	 */
	public function getFilePath($item)
	{
		if($this->_areaModule !== null)
		{
			return $this->_areaModule->getFilePath($item);
		}
		return $this->_primaryModule->getFilePath($item);
	} // end getFilePath();

	/**
	 * Returns the primary module.
	 *
	 * @return \Trinity\Basement\Module
	 */
	public function getPrimaryModule()
	{
		return $this->_primaryModule;
	} // end getPrimaryModule();


	/**
	 * Returns the area module.
	 *
	 * @return \Trinity\Basement\Module
	 */
	public function getAreaModule()
	{
		return $this->_areaModule;
	} // end getModule();

	/**
	 * Returns the name of the service controller to be used by
	 * this area.
	 *
	 * @return string
	 */
	public function getController()
	{
		return $this->_options['controller'];
	} // end getController();

	/**
	 * Performs the option validation.
	 *
	 * @throws Area_Exception
	 * @param array $opts The options to validate
	 */
	private function _validateOptions(array $opts)
	{
		if(!isset($opts['controller']))
		{
			throw new Area\Exception('The area does not define any controller.');
		}
	} // end _validateOptions();
} // end Area;