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
	 * The module path.
	 * @var string
	 */
	private $_path;

	/**
	 * Creates the module.
	 * 
	 * @param string $name The module name
	 * @param string $path The path to the module files.
	 */
	public function __construct($name, $path)
	{
		$this->_name = $name;

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
	 * The method is called during the module initialization.
	 * 
	 * @param BaseApplication $application The application link.
	 */
	public function onInit(BaseApplication $application)
	{
		/* empty */
	} // end onInit();
} // end Module;