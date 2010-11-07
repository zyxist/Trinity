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
namespace Trinity\Web\Session;
use \IteratorAggregate;
use \ArrayIterator;
use \Countable;

/**
 * Represents a single session group where the user is allowed to store
 * the data. Note that the group is a valid model, so it can be exlicitely
 * accessed by views.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
class Group implements IteratorAggregate, Countable
{
	/**
	 * Session namespace name
	 * @var string
	 */
	private $_name;

	/**
	 * The list of namespaces, where the variable lifetimes have
	 * already been calculated.
	 * @var array
	 */
	static private $_calculated = array();

	/**
	 * Constructs the session group. Note that you should not call this
	 * constructor explicitely, but rather use a proper method in <tt>Session</tt>
	 * object.
	 *
	 * @param string $name The namespace name
	 */
	public function __construct($name)
	{
		$this->_name = (string)$name;
	} // end __construct();

	/**
	 * Returns the session variable with the specified name.
	 *
	 * @param string $name Session variable name
	 * @return mixed
	 */
	public function &__get($name)
	{
		$this->_validateNamespace();
		if(!isset($_SESSION[$this->_name]['d'][(string)$name]))
		{
			$_SESSION[$this->_name]['d'][(string)$name] = null;
		}
		return $_SESSION[$this->_name]['d'][(string)$name];
	} // end __get();

	/**
	 * Sets the session variable to the specified value.
	 * 
	 * @param string $name Session variable name
	 * @param mixed $value New value
	 */
	public function __set($name, $value)
	{
		$this->_validateNamespace();
		$_SESSION[$this->_name]['d'][(string)$name] = $value;
	} // end __set();

	/**
	 * Checks if the specified session variable exists.
	 *
	 * @param string $name Session variable name
	 * @return boolean
	 */
	public function __isset($name)
	{
		$this->_validateNamespace();
		return isset($_SESSION[$this->_name]['d'][(string)$name]);
	} // end __isset();

	/**
	 * Removes the specified session variable.
	 * 
	 * @param string $name Session variable name
	 */
	public function __unset($name)
	{
		$this->_validateNamespace();
		if(isset($_SESSION[$this->_name]['d'][(string)$name]))
		{
			unset($_SESSION[$this->_name]['d'][(string)$name]);
		}
		if(isset($_SESSION[$this->_name]['m'][(string)$name]))
		{
			unset($_SESSION[$this->_name]['m'][(string)$name]);
		}
	} // end __unset();

	/**
	 * Returns the iterator through the namespace session variable
	 * collection.
	 * 
	 * @return ArrayIterator
	 */
	public function getIterator()
	{
		$this->_validateNamespace();
		return new ArrayIterator($_SESSION[$this->_name]['d']);
	} // end getIterator();

	/**
	 * Counts the number of elements in this group.
	 * 
	 * @return integer
	 */
	public function count()
	{
		if(!isset($_SESSION[$this->_name]))
		{
			return 0;
		}
		return sizeof($_SESSION[$this->_name]['d']);
	} // end count();

	/**
	 * Sets the session variable lifetime. After the specified number of
	 * requests (hops) the variable is removed from the scope automatically.
	 * @param string $name Session variable name
	 * @param int $hops The number of hops
	 */
	public function setLifetime($name, $hops)
	{
		$this->_validateNamespace();
		$_SESSION[$this->_name]['m'][(string)$name] = (int)$hops;
	} // end setLifetime();

	/**
	 * Validates the group data within <tt>$_SESSION</tt> table, optionally
	 * initializing it with the correct values.
	 */
	private function _validateNamespace()
	{
		if(!isset($_SESSION[$this->_name]))
		{
			$_SESSION[$this->_name] = array('d' => array(), 'm' => array());
		}
		if(!isset($_SESSION[$this->_name]['d']))
		{
			$_SESSION[$this->_name]['d'] = array();
		}
		if(!isset($_SESSION[$this->_name]['m']))
		{
			$_SESSION[$this->_name]['m'] = array();
		}
		if(!isset(self::$_calculated[$this->_name]))
		{
			$this->_calculate();
		}
	} // end _validateNamespace();

	/**
	 * Calculates the session variable lifetimes.
	 */
	private function _calculate()
	{
		foreach($_SESSION[$this->_name]['m'] as $name => &$hops)
		{
			if($hops < 1)
			{
				if(isset($_SESSION[$this->_name]['d'][$name]))
				{
					unset($_SESSION[$this->_name]['d'][$name]);
				}
				unset($_SESSION[$this->_name]['m'][$name]);
			}
			else
			{
				$hops--;
			}
		}
		self::$_calculated[$this->_name] = true;
	} // end _calculate();

} // end Group;