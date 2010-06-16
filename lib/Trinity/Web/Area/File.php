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
 * The standard area implementation which loads the area definitions from
 * an INI file. After creation, the object represents the discovered area.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
class Area_File extends Area_Abstract
{
	/**
	 * The area module
	 * @var Module
	 */
	private $_module;

	/**
	 * Performs an area discovery from a file.
	 *
	 * @throws Area_Exception
	 * @param string $areaDefFile Area definition file
	 * @param string $currentHost Current host to perform the match
	 */
	public function __construct(BaseApplication $application, $areaDefFile, $currentHost)
	{
		parent::__construct($application);

		if(!file_exists($areaDefFile))
		{
			throw new Area_Exception('Cannot load area definitions from '.$areaDefFile.': file not accessible.');
		}
		$data = parse_ini_file($areaDefFile, true);

		$found = false;
		foreach($data as $name => $area)
		{
			if(!isset($area['host']))
			{
				throw new Area_Exception('Missing \'host\' attribute in '.$name.' area.');
			}

			if(preg_match($area['host'], $currentHost))
			{
				$this->_name = $name;
				// TODO: Needs to be fixed.
				$this->_path = $application->getModulePath();
				$this->_validateOptions($area);
				$this->_options = $area;
				$found = true;
				break;
			}
		}
		if(!$found)
		{
			throw new Area_Exception('No area matches the host '.$currentHost);
		}
	} // end __construct();

	/**
	 * Selects the area module.
	 *
	 * @param string $moduleName The name of the module
	 */
	public function setModule($moduleName)
	{
		$this->_module = $this->_application->loadModule($this->_name.'.'.ucfirst($moduleName));
	} // end setModule();

	/**
	 * Returns the path for the code within the given area.
	 *
	 * @param string $item Directory name
	 * @return string
	 */
	public function getCodePath($item)
	{
		if($this->_module !== null)
		{
			return $this->_module->getCodePath($item);
		}
		return parent::getCodePath($item);
	} // end getCodePath();

	/**
	 * Returns the path for the data item within the given area.
	 *
	 * @param string $item Directory name
	 * @return string
	 */
	public function getFilePath($item)
	{
		if($this->_module !== null)
		{
			return $this->_module->getFilePath($item);
		}
		return parent::getFilePath($item);
	} // end getFilePath();

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
			throw new Area_Exception('The area does not define any controller.');
		}
	} // end _validateOptions();
} // end Area_File;