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

/**
 * A single application module. The class must be extended by modules.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
abstract class Module
{
	protected $_name;
	protected $_namespacePrefix;
	protected $_directory;
	protected $_reflection;

	/**
	 * The service locator
	 * @var \Trinity\Basement\ServiceLocator
	 */
	protected $_serviceLocator;

	public function setServiceLocator(ServiceLocator $serviceLocator)
	{
		$this->_serviceLocator = $serviceLocator;
	} // end setServiceLocator();

	public function getServiceLocator()
	{
		return $this->_serviceLocator;
	} // end getServiceLocator();

	/**
	 * This method should return a service container used with this application.
	 *
	 * @return ServiceContainer
	 */
	public function registerServiceContainer()
	{
		return null;
	} // end registerServiceContainer();

	/**
	 * Here goes the launching code.
	 */
	public function launch()
	{
		/* null */
	} // end launch();

	/**
	 * Here goes the shutdown code.
	 */
	public function shutdown()
	{
		/* null */
	} // end shutdown();

	/**
	 * Returns the module name.
	 *
	 * @return string
	 */
	public function getName()
	{
		if(null === $this->_name)
		{
			$this->initReflection();
		}
		return $this->_name;
	} // end getName();

	/**
	 * Returns the module namespace prefix.
	 *
	 * @return string
	 */
	public function getNamespacePrefix()
	{
		if(null === $this->_namespacePrefix)
		{
			$this->initReflection();
		}
		return $this->_namespacePrefix;
	} // end getName();

	/**
	 * Returns the  module directory with the appended
	 * trailing slash.
	 *
	 * @return string
	 */
	public function getDirectory()
	{
		if(null === $this->_directory)
		{
			$this->initReflection();
		}
		return $this->_directory;
	} // end getDirectory();

	/**
	 * Returns the reflection object for this module.
	 *
	 * @return \ReflectionObject
	 */
	public function getReflectionObject()
	{
		if(null === $this->_reflectionObject)
		{
			$this->initReflection();
		}
		return $this->_reflectionObject;
	} // end getReflectionObject();

	/**
	 * Creates the reflection information about the module.
	 */
	public function initReflection()
	{
		$this->_reflectionObject = new \ReflectionObject($this);

		$this->_name = str_replace('\\', '', $this->_namespacePrefix = $this->_reflectionObject->getNamespaceName());
		$this->_directory = dirname($this->_reflectionObject->getFileName()).DIRECTORY_SEPARATOR;
	} // end initReflection();
} // end Module;