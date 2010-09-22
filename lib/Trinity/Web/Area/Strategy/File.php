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
namespace Trinity\Web\Area\Strategy;
use \Trinity\Basement\Application as BaseApplication;
use \Trinity\Basement\Module as Module;
use \Trinity\Web\Area\Strategy;
use \Trinity\Web\Area\Exception as Area_Exception;

/**
 * The area discovery strategy that stores area definitions in files.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
class File implements Strategy
{
	/**
	 * Discover the area name from host.
	 */
	const DISCOVERY_HOST = 0;
	/**
	 * Discover the area from query path
	 */
	const DISCOVERY_QUERY_PATH = 1;

	/**
	 * The discovery type.
	 * @var int
	 */
	private $_discoveryType;

	/**
	 * The discovery data
	 * @var string
	 */
	private $_discoveryData;

	/**
	 * The file name.
	 * @var string
	 */
	private $_fileName;

	/**
	 * The name of the default area.
	 * @var string
	 */
	private $_defaultArea = null;

	/**
	 * Name of the discoveried area.
	 */
	private $_discoveriedArea = null;

	/**
	 * The list of available areas.
	 * @var array
	 */
	private $_areas;


	/**
	 * Constructs the strategy object.
	 *
	 * @throws Area_Exception
	 * @param string $fileName Area definition file
	 */
	public function __construct($fileName)
	{
		if(!file_exists($fileName))
		{
			throw new Area_Exception('Cannot load area definitions from '.$fileName.': file not accessible.');
		}
		$this->_fileName = $fileName;
	} // end __construct();

	/**
	 * Sets the name of the default area.
	 *
	 * @param string $defaultArea Default area name
	 * @return Strategy_File Fluent interface.
	 */
	public function setDefaultArea($defaultArea)
	{
		$this->_defaultArea = (string)$defaultArea;
		return $this;
	} // end setDefaultArea();

	/**
	 * Sets the requested discovery type.
	 *
	 * @param int $type Discovery type
	 * @param Opc\Visit $visit Visit object used to grab the discovery type data.
	 * @return Strategy_File Fluent interface.
	 */
	public function setDiscoveryType($type, \Opc\Visit $visit)
	{
		$this->_discoveryType = (int)$type;

		switch($type)
		{
			case 0:
				$this->_discoveryData = $visit->currentHost;
				break;
			case 1:
				$this->_discoveryData = $visit->currentParams;
				break;
			default:
				throw new Area_Exception('Unknown discovery type: '.$type.'.');
		}
		return $this;
	} // end setDiscoveryType();

	/**
	 * Returns the discovery type.
	 *
	 * @return int
	 */
	public function getDiscoveryType()
	{
		return $this->_discoveryType;
	} // end getDiscoveryType();

	/**
	 * Returns the information about the specified area.
	 *
	 * @throws Area_Exception
	 * @param string $name Area name
	 * @return array
	 */
	public function getAreaOptions($name)
	{
		if($this->_areas === null)
		{
			$this->_loadAreas();
		}
		if(!isset($this->_areas[$name]))
		{
			throw new Area_Exception('Unknown area: '.$name.'.');
		}
		return $this->_areas[$name];
	} // end getAreaOptions();

	/**
	 * Discoveries the area from a file.
	 *
	 * @return array
	 */
	public function discoverArea()
	{
		if($this->_areas === null)
		{
			$this->_loadAreas();
		}
		if($this->_discoveriedArea !== null)
		{
			// TODO: What is that $name?
			return array(/*$name*/'', $this->_areas[$this->_discoveriedArea]);
		}
		if($this->_discoveryType == self::DISCOVERY_HOST)
		{
			return $this->_discoveryHost();
		}
		else
		{
			return $this->_discoveryQueryPath();
		}
	} // end discoverArea();

	/**
	 * Discoveries the area from host.
	 *
	 * @return array
	 */
	private function _discoveryHost()
	{
		foreach($this->_areas as $name => $area)
		{
			if(!isset($area['host']))
			{
				throw new Area_Exception('Missing \'host\' attribute in '.$name.' area.');
			}

			if(preg_match($area['host'], $this->_discoveryData))
			{
				$this->_discoveriedArea = $name;
				return array($name, $area);
			}

			if($name == $this->_defaultArea)
			{
				$store = array($name, $area);
			}
		}
		// Throw an exception or return the default area.
		if(!isset($store))
		{
			throw new Area_Exception('No area matches the host '.$this->_discoveryData);
		}
		$this->_discoveriedArea = $name;
		return $store;
	} // end _discoveryHost();

	/**
	 * Discoveries the area from query path.
	 *
	 * @return array
	 */
	private function _discoveryQueryPath()
	{
		foreach($this->_areas as $name => $area)
		{
			if(!isset($area['path']))
			{
				throw new Area_Exception('Missing \'path\' attribute in '.$name.' area.');
			}
			if(stripos($this->_discoveryData, '/'.$area['path']) === 0)
			{
				$this->_discoveriedArea = $name;
				return array($name, $area);
			}
			if($name == $this->_defaultArea)
			{
				$store = array($name, $area);
			}
		}
		// Throw an exception or return the default area.
		if(!isset($store))
		{
			throw new Area_Exception('No area matches the query path '.$this->_discoveryData);
		}
		$this->_discoveriedArea = $name;
		return $store;
	} // end _discoveryHost();

	private function _loadAreas()
	{
		$this->_areas = parse_ini_file($this->_fileName, true);
		if(!is_array($this->_areas))
		{
			throw new Area_Exception('Error while loading the area definition file.');
		}
	} // end _loadAreas();
} // end File;