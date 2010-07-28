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

namespace Trinity\Web\Controller;

/**
 * A state keeper for controllers and bricks.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
class State
{
	/**
	 * Keeps the state variables.
	 *
	 * @var array
	 */
	private $_vars = array();

	/**
	 * Sets a new value of the state variable.
	 *
	 * @param string $name The variable name.
	 * @param mixed $value The variable value.
	 */
	public function __set($name, $value)
	{
		$this->_vars[$name] = $value;
	} // end __set();

	/**
	 * Returns the state variable value. If the variable is not defined, an
	 * exception is thrown.
	 *
	 * @throws State_Exception
	 * @param string $name The state variable name
	 * @return mixed
	 */
	public function __get($name)
	{
		if(!isset($this->_vars[$name]))
		{
			throw new State_Exception('The key '.$name.' does not exist in the controller state manager.');
		}
		return $this->_vars[$name];
	} // end __get();

	/**
	 * Checks if the specified state variable actually exists.
	 *
	 * @param string $name The variable name.
	 * @return boolean
	 */
	public function __isset($name)
	{
		return isset($this->_vars[$name]);
	} // end __isset();

	/**
	 * Removes a state variable with the specified name. If the variable does
	 * not exist, an exception is thrown.
	 *
	 * @throws State_Exception
	 * @param string $name The state variable name.
	 */
	public function __unset($name)
	{
		if(!isset($this->_vars[$name]))
		{
			throw new State_Exception('The key '.$name.' does not exist in the controller state manager.');
		}
		unset($this->_vars[$name]);
	} // end __unset();

	/**
	 * As arguments, the programmer specified the names of state variables. The
	 * method checks each of them and returns the value of the first that exists.
	 * If neither of the variables is available, an exception is thrown.
	 *
	 * @throws State_Exception
	 * @param string ... The variable names to check.
	 * @return mixed
	 */
	public function first()
	{
		$keys = func_get_args();

		foreach($keys as $key)
		{
			if(isset($this->_vars[$key]))
			{
				return $this->_vars[$key];
			}
		}
		throw new State_Exception('None of the keys '.implode(', ', $keys).' exists in the controller state manager.');
	} // end first();

	/**
	 * Returns an array with all the state variables that are currently set.
	 *
	 * @return array
	 */
	public function getAll()
	{
		return $this->_vars;
	} // end getAll();
} // end State;