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
namespace Trinity\Basement\Module;
use \Trinity\Basement\Module;

/**
 * The module manager is responsible for loading and tracking application
 * modules.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
class Manager
{
	/**
	 * The list of currently loaded modules.
	 * @var array
	 */
	private $_modules = array();

	/**
	 * The module namespace.
	 * @var string
	 */
	private $_namespace = null;

	/**
	 * The module base path.
	 * @var string
	 */
	private $_basePath = null;

	/**
	 * Creates a new module manager.
	 *
	 * @param string $namespace The module namespace.
	 * @param string $basePath The path to the modules.
	 */
	public function __construct($namespace, $basePath)
	{
		$this->_namespace = $namespace;
		$this->_basePath = $basePath;
	} // end __construct();

	public function getNamespace()
	{
		return $this->_namespace;
	} // end getNamespace();

	public function getBasePath()
	{
		return $this->_basePath;
	} // end getBasePath();

	public function setModule(Module $module)
	{
		$this->_modules[$module->getName()] = $module;
	} // end setModule();

	public function getModule($module)
	{
		if(isset($this->_modules[$module]))
		{
			return $this->_modules[$module];
		}

		// Prepare the module namespace.
		$namespace = str_replace('.', '\\', $module);
		if($this->_namespace != '')
		{
			$namespace = str_replace('.', '\\', $this->_namespace.($module != '' ? '.'.$module : ''));
		}

		// Preparing the module path.
		$rest = strrchr($namespace, '\\');
		$path =
			$this->_basePath.
			str_replace('\\', '/', substr($namespace, 0, strlen($namespace) - strlen($rest))).
			str_replace(array('_',  '\\'), '/', $rest).'/';

		// Try to find the module object.
		return $this->_modules[$module] = $this->_getModuleObject($module, $namespace, $path);
	} // end getModule();

	/**
	 * Disposes the memory.
	 */
	public function dispose()
	{
		foreach($this->_modules as $module)
		{
			$module->dispose();
		}
	} // end dispose();

	/**
	 * Creates and builds the module object.
	 *
	 * @todo Add caching.
	 * @param string $name The module name.
	 * @param string $namespace The module namespace
	 * @param string $path The module path
	 * @return \Trinity\Basement\Module
	 */
	private function _getModuleObject($name, $namespace, $path)
	{
		if(file_exists($path.'/Module.php'))
		{
			require($path.'/Module.php');
			$className = $namespace.'\\Module';

			if(!class_exists($className, false))
			{
				throw new \Trinity\Basement\Module\Exception('The module file does not contain any module class.');
			}
			$object = new $className($this, $name, $namespace, $path);

			if(!$object instanceof Module)
			{
				throw new \Trinity\Basement\Module\Exception('The module object is not an instance of \\Trinity\\Basement\\Module.');
			}

			return $object;
		}
		else
		{
			return new Module($this, $name, $namespace, $path);
		}
	} // end _getModuleObject();
} // end Manager;
