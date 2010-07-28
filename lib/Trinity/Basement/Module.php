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
namespace Trinity\Basement;
use \Trinity\Basement\Application as BaseApplication;
use \Trinity\Basement\Module\Manager;

/**
 * Represents a single module and provides utilities for managing them. The
 * concrete modules may extend this class to provide some extra utilities.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
class Module
{
	/**
	 * The module name.
	 * @var string
	 */
	private $_name;

	/**
	 * The module namespace.
	 * @var string
	 */
	private $_namespace;

	/**
	 * The module path.
	 * @var string
	 */
	private $_path;

	/**
	 * The module manager.
	 * @var \Trinity\Basement\Module\Manager
	 */
	private $_manager;

	/**
	 * Creates the module.
	 *
	 * @param \Trinity\Basement\Module\Manager $manager The module manager.
	 * @param string $name The module name
	 * @param string $namespace The module fully qualified namespace
	 * @param string $path The path to the module files.
	 */
	public function __construct(Manager $manager, $name, $namespace, $path)
	{
		$this->_manager = $manager;
		$this->_name = $name;
		$this->_namespace = $namespace;

		if($path[strlen($path) - 1] != '/')
		{
			$path .= '/';
		}

		$this->_path = $path;
		$this->onInit(BaseApplication::getApplication());
	} // end __construct();

	/**
	 * Returns the name of the module.
	 *
	 * @return string
	 */
	public function getName()
	{
		return $this->_name;
	} // end getName();

	/**
	 * Returns the module path.
	 *
	 * @return string
	 */
	public function getPath()
	{
		return $this->_path;
	} // end getName();

	/**
	 * Returns the namespace of this module.
	 *
	 * @return string
	 */
	public function getNamespace()
	{
		return $this->_namespace;
	} // end getNamespace();

	/**
	 * Returns the path to the code directory within a module.
	 * 
	 * @param string $item The directory name.
	 * @return string
	 */
	public function getCodePath($item)
	{
		return $this->_path.ucfirst($item).'/';
	} // end getCodePath();

	/**
	 * Returns the path to the data directory within a module.
	 * 
	 * @param string $item The directory name.
	 * @return string
	 */
	public function getFilePath($item)
	{
		return $this->_path.$item.'/';
	} // end getFilePath();

	/**
	 * Returns an object of the submodule.
	 *
	 * @param string $item The module item.
	 * @return \Trinity\Basement\Module
	 */
	public function getSubmodule($item)
	{
		if($this->_name != '')
		{
			return $this->_manager->getModule($this->_name.'.'.$item);
		}
		else
		{
			return $this->_manager->getModule($item);
		}
	} // end getSubmodule();

	/**
	 * Loads the specified PHP file within a module. Before loading,
	 * the method checks if the file actually exists. Returns true,
	 * if the file was successfully loaded, and false otherwise.
	 *
	 * @param string $fileName The file name (without an extension)
	 * @return boolean
	 */
	public function loadFile($fileName)
	{
		if(!file_exists($this->_path.$fileName.'.php'))
		{
			return false;
		}
		require($this->_path.$fileName.'.php');
		return true;
	} // end loadFile();

	/**
	 * Returns the fully qualified class name for the given module.
	 *
	 * @param string $className The relative class name
	 * @return string
	 */
	public function getClassName($className)
	{
		return $this->_namespace.'\\'.$className;
	} // end getClassName();

	/**
	 * The method is called during the module initialization.
	 * 
	 * @param BaseApplication $application The application link.
	 */
	public function onInit(BaseApplication $application)
	{
		/* empty */
	} // end onInit();

	/**
	 * Allows to free the memory.
	 */
	public function dispose()
	{
		$this->_manager = null;
	} // end dispose();
} // end Module;