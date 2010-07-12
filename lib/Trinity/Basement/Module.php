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

/**
 * Represents a single module. The class is not obligatory for the modules,
 * but can be used to store some extra operations.
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
	 * The fully qualified module namespace.
	 * @var string
	 */
	private $_namespacePrefix;

	/**
	 * The module path.
	 * @var string
	 */
	private $_path;

	/**
	 * Creates the module.
	 * 
	 * @param string $name The module name
	 * @param string $namespace The module fully qualified namespace prefix
	 * @param string $path The path to the module files.
	 */
	public function __construct($name, $path, $namespacePrefix = '')
	{
		$this->_name = $name;

		if($path[strlen($path) - 1] != '/')
		{
			$path .= '/';
		}
		$this->_path = $path;
		$this->_namespacePrefix = $namespacePrefix;

		if($this->_namespacePrefix !== '' && $namespacePrefix[strlen($namespacePrefix) - 1] != '\\')
		{
			$this->_namespacePrefix .= '\\';
		}

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
	 * Returns the namespace prefix of this module.
	 *
	 * @return string
	 */
	public function getNamespacePrefix()
	{
		return $this->_namespacePrefix;
	} // end getNamespacePrefix();

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
		return new Module($this->getName().'.'.$item, $this->getPath().'/'.$item.'/', $this->_namespacePrefix);
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
		return $this->_namespacePrefix.str_replace('.', '\\', $this->_name).'\\'.$className;
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
} // end Module;