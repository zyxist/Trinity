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
 * The view abstract class used for whatever we want.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
abstract class View
{
	/**
	 * The list of registered models.
	 * @var array
	 */
	private $_models = array();

	/**
	 * The data passed to the view.
	 * @var array
	 */
	private $_data = array();

	/**
	 * Returns the data with the specified key. If the key does
	 * not exist, it returns NULL.
	 *
	 * @param string $name The data key
	 * @return mixed Value
	 */
	public function get($name)
	{
		if(!isset($this->_data[$name]))
		{
			return null;
		}
		return $this->_data[$name];
	} // end get();

	/**
	 * Assigns the specified value to the key.
	 *
	 * @param string $name The key we want to assign the data to.
	 * @param string $value The new key value.
	 */
	public function set($name, $value)
	{
		$this->_data[$name] = $value;
	} // end set();

	/**
	 * Checks if the specified data key exists.
	 *
	 * @param name $name The key to check
	 * @return boolean True, if the key exists.
	 */
	public function exists($name)
	{
		return isset($this->_data[$name]);
	} // end exists();

	/**
	 * Binds a new model to the view under the specified name.
	 *
	 * @throws Exception
	 * @param string $name The name of the model
	 * @param $model The concrete model object.
	 */
	public function addModel($name, $model)
	{
		if(isset($this->_models[$name]))
		{
			throw new Exception('The model with the name '.$name.' already exists.');
		}
		$this->_models[$name] = $model;
	} // end addModel();

	/**
	 * Checks if there is a model assigned for the specified
	 * name.
	 *
	 * @param string $name The model name.
	 * @return boolean True, if the model name is assigned.
	 */
	public function hasModel($name)
	{
		return isset($this->_models[$name]);
	} // end hasModel();

	/**
	 * Returns the model assigned to the specified name in
	 * the view.
	 *
	 * @throws Exception
	 * @param string $name The model name
	 * @param string $contract The contract that must be passed.
	 * @return Model The model object
	 */
	public function getModel($name, $contract = null)
	{
		if(!isset($this->_models[$name]))
		{
			throw new Exception('The model '.$name.' does not exist.');
		}

		if($contract !== null)
		{
			if(!is_a($this->_models[$name], $contract))
			{
				throw new Exception('The model '.$name.' does not satisfy the contract '.$contract);
			}
		}

		return $this->_models[$name];
	} // end getModel();

	/**
	 * Removes the model assigned to the specified name. If
	 * the name is not assigned, it throws an exception.
	 *
	 * @throws Exception
	 * @param string $name The model name
	 */
	public function removeModel($name)
	{
		if(!isset($this->_models[$name]))
		{
			throw new Exception('The model '.$name.' does not exist.');
		}
		unset($this->_models[$name]);
	} // end removeModel();

	/**
	 * Assigns a model object to the name in the view. If the model
	 * name is already assigned, it overwrites it.
	 *
	 * @param string $name The model name
	 * @param Model $model The model object
	 */
	public function replaceModel($name, Model $model)
	{
		$this->_models[$name] = $model;
	} // end replaceModel();
} // end View;