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
namespace Trinity\Web\Area;
use \Trinity\Basement\Application as BaseApplication;
use \Trinity\Basement\Module as Module;
use \Trinity\Web\Area_Exception;

/**
 * The area discovery strategy that stores area definitions in files.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
class Strategy_File implements Strategy_Interface
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
	 * Sets the requested discovery type.
	 *
	 * @param int $type Discovery type
	 * @param Opc_Visit $visit Visit object used to grab the discovery type data.
	 */
	public function setDiscoveryType($type, \Opc_Visit $visit)
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
	 * Discoveries the area from a file.
	 *
	 * @return array
	 */
	public function discoverArea()
	{
		$data = parse_ini_file($this->_fileName, true);
		if($this->_discoveryType == self::DISCOVERY_HOST)
		{
			return $this->_discoveryHost($data);
		}
		else
		{
			return $this->_discoveryQueryPath($data);
		}
	} // end discoverArea();

	/**
	 * Discoveries the area from host.
	 *
	 * @param string $data The loaded area description file.
	 * @return array
	 */
	private function _discoveryHost($data)
	{
		foreach($data as $name => $area)
		{
			if(!isset($area['host']))
			{
				throw new Area_Exception('Missing \'host\' attribute in '.$name.' area.');
			}

			if(preg_match($area['host'], $this->_discoveryData))
			{
				return array($name, $area);
			}
		}
		throw new Area_Exception('No area matches the host '.$this->_discoveryData);
	} // end _discoveryHost();

	/**
	 * Discoveries the area from query path.
	 *
	 * @param string $data The loaded area description file.
	 * @return array
	 */
	private function _discoveryQueryPath($data)
	{
		foreach($data as $name => $area)
		{
			if(!isset($area['host']))
			{
				throw new Area_Exception('Missing \'host\' attribute in '.$name.' area.');
			}
			if(stripos($this->_discoveryData, '/'.$name) === 0)
			{
				return array($name, $area);
			}
		}
		throw new Area_Exception('No area matches the query path '.$this->_discoveryData);
	} // end _discoveryHost();
} // end Area_File;