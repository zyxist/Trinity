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
use \Trinity\Basement\Module;
use \Trinity\Cache\Cache;
use \Trinity\Web\Area;

/**
 * The class manages the list of areas and modules, providing the
 * mapping between them.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
class Manager
{
	const USE_EXCEPTION = 0;
	const IGNORE = 1;

	/**
	 * Maps the modules for areas.
	 * @var array
	 */
	protected $_moduleMap = array();

	/**
	 * The active area.
	 * @var \Trinity\Web\Area
	 */
	protected $_activeArea;

	/**
	 * The active module
	 * @var \Trinity\Web\Module
	 */
	protected $_activeModule;
	
	/**
	 * If the modules are tied to areas.
	 * @var boolean
	 */
	protected $_modulesTiedToAreas = true;

	/**
	 * The area metadata.
	 * @var array
	 */
	protected $_areaMetadata = array();
	
	/**
	 * The metadata loader.
	 * @var \Trinity\Web\Area\MetadataLoader
	 */
	protected $_metadataLoader;

	/**
	 * The cache system.
	 * @var \Trinity\Cache\Cache
	 */
	protected $_cache;

	/**
	 * Constructs the area manager.
	 *
	 * @param Cache $cache The caching system.
	 * @param MetadataLoader $metadataLoader The metadata loader.
	 */
	public function __construct(Cache $cache, MetadataLoader $metadataLoader)
	{
		$this->_cache = $cache;
		$this->_metadataLoader = $metadataLoader;
	} // end __construct();

	/**
	 * Sets if the modules are tied to areas or are free. Implements
	 * fluent interface.
	 *
	 * @param boolean $status The new option status.
	 * @return Manager
	 */
	public function setModulesTiedToAreas($status)
	{
		$this->_modulesTiedToAreas = (boolean)$status;

		return $this;
	} // end setModulesTiedToAreas();

	/**
	 * Returns true, if the modules are tied to areas.
	 *
	 * @return boolean
	 */
	public function getModulesTiedToAreas()
	{
		return $this->_modulesTiedToAreas;
	} // end getModulesTiedToAreas();

	/**
	 * Registers the module in the area. The registration gives us a connection
	 * that this specified module provides some stuff that should be displayed
	 * in the specified area.
	 *
	 * If the modules are not tied to areas, the area key is ignored.
	 *
	 * Implements fluent interface.
	 *
	 * @param Module $module The module object.
	 * @param string $moduleKey The module key.
	 * @param string $areaKey The area key.
	 * @return Manager
	 */
	public function registerModuleForArea(Module $module, $moduleKey, $areaKey)
	{
		if($this->_modulesTiedToAreas)
		{
			if(!isset($this->_moduleMap[$areaKey]))
			{
				$this->_moduleMap[$areaKey] = array();
			}
			$this->_moduleMap[$areaKey][$moduleKey] = $module;
		}
		else
		{
			$this->_moduleMap[$moduleKey] = $module;
		}
		return $this;
	} // end registerModuleForArea();

	/**
	 * Selects the active area and injects the metadata to it.
	 *
	 * @param Area $area The area
	 */
	public function setActiveArea(Area $area)
	{
		$this->_activeArea = $area;
		$area->setMetadata($this->getAreaMetadata($area->getAreaName()));
	} // end setActiveArea();

	/**
	 * Sets the active module. If the modules are tied to areas, the module
	 * must be registered for the selected active area.
	 * 
	 * If no active area is selected, an exception is thrown.
	 *
	 * @throws \Trinity\Web\Area\Exception
	 * @param string $moduleKey The module key.
	 */
	public function setActiveModule($moduleKey)
	{
		$moduleKey = (string)$moduleKey;

		if($this->_activeArea === null)
		{
			throw new Exception('Cannot select the \''.$moduleKey.'\' module: no active area is selected.');
		}

		if($this->_modulesTiedToAreas)
		{
			if(isset($this->_moduleMap[$this->_activeArea->getAreaName()]))
			{
				if(isset($this->_moduleMap[$this->_activeArea->getAreaName()][$moduleKey]))
				{
					$this->_activeModule = $this->_moduleMap[$this->_activeArea->getAreaName()][$moduleKey];
				}
				else
				{
					throw new Exception('Unknown module: \''.$moduleKey.'\' in area \''.$this->_activeArea->getName().'\'');
				}
			}
			else
			{
				throw new Exception('Unknown area settings: \''.$this->_activeArea->getAreaName().'\'');
			}
		}
		else
		{
			if(isset($this->_moduleMap[$moduleKey]))
			{
				$this->_activeModule = $this->_moduleMap[$moduleKey];
			}
			else
			{
				throw new Exception('Unknown module \''.$moduleKey.'\'');
			}
		}
	} // end setActiveModule();

	/**
	 * Returns the active area object.
	 * @return \Trinity\Web\Area
	 */
	public function getActiveArea()
	{
		return $this->_activeArea;
	} // end getActiveArea();

	/**
	 * Returns the active module object.
	 * 
	 * @return \Trinity\Basement\Module
	 */
	public function getActiveModule()
	{
		return $this->_activeModule;
	} // end getActiveModule();

	public function hasModule($moduleKey)
	{
		if($this->_activeArea === null)
		{
			throw new Exception('Cannot select the \''.$moduleKey.'\' module: no active area is selected.');
		}
	} // end hasModule();

	/**
	 * Returns the metadata for the given area.
	 *
	 * @param string $areaKey The area key.
	 * @return array
	 */
	public function getAreaMetadata($areaKey)
	{
		if(!isset($this->_areaMetadata[$areaKey]))
		{
			$key = 'trinity:area:metadata:'.$areaKey;
			if($this->_metadataLoader->isPreloaded() || !$this->_cache->has($key))
			{
				$data = $this->_metadataLoader->loadMetadata($areaKey);
				if($data === null)
				{
					throw new \DomainException('Cannot load area metadata: the area \''.$areaKey.'\' has no metadata defined.');
				}
				$this->_cache->set($key, $this->_areaMetadata[$areaKey] = $data);
			}
			else
			{
				$this->_areaMetadata[$areaKey] = $this->_cache->get($key);
			}
		}
		return $this->_areaMetadata[$areaKey];
	} // end getAreaMetadata();
} // end Manager;