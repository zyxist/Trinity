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
 * The basic object locator for the framework that can be freely extended
 * to match programmer's needs.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
class Locator
{
	/**
	 * The locator name used to fire events.
	 * @var string
	 */
	protected $_name;

	/**
	 * The object pool.
	 * @var array
	 */
	protected $_pool = array();

	/**
	 * Creates the locator.
	 *
	 * @param string $name The locator name
	 */
	public function __construct($name)
	{
		$this->_name = (string)$name;
	} // end __construct();

	/**
	 * Returns the object locator name.
	 *
	 * @return string
	 */
	public function getName()
	{
		return $this->_name;
	} // end getName();

	/**
	 * Adds a new object to the pool.
	 *
	 * @throws Core_Exception
	 * @param string $name The object name.
	 * @param object $object The object to store.
	 */
	public function set($name, $object)
	{
		$this->_verify($object);
		$this->_pool[(string)$name] = $object;
	} // end set();

	/**
	 * Checks if there is an object registered under the specified name.
	 *
	 * @return boolean
	 */
	public function exists($name)
	{
		return isset($this->_pool[(string)$name]);
	} // end exists();

	/**
	 * Returns the object stored in the locator under the specified name.
	 * If the creator function is specified, the object may be created on
	 * demand if it is not available yet.
	 *
	 * @param string $name The object name.
	 * @return object
	 */
	public function get($name)
	{
		if(!isset($this->_pool[(string)$name]))
		{
			$this->_objectMissing((string)$name);
		}
		return $this->_pool[(string)$name];
	} // end get();

	/**
	 * This method should check if the specified object can be inserted in this
	 * locator.
	 *
	 * @param object $object The object to verify.
	 */
	protected function _verify($object)
	{
		/* don't verify */
	} // end _verify();

	/**
	 * The action fired when an object is missing. The default implementation
	 * always throws an exception.
	 *
	 * @throws Core_Exception
	 * @param string $name The missing object name.
	 */
	protected function _objectMissing($name)
	{
		throw new Exception('The object '.$name.' is missing in the locator '.$this->_name);
	} // end _objectMissing();
} // end Locator;