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
 * The configuration storage.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
class Config
{
	/**
	 * The configuration options and their values.
	 * @var array
	 */
	protected $_opts = array();

	/**
	 * Returns the specified configuration option. If the option does not
	 * exist, an exception is thrown.
	 *
	 * @throws \Trinity\Basement\Exception
	 * @param string $name The option name.
	 * @return mixed
	 */
	public function get($name)
	{
		if(!array_key_exists($name, $this->_opts))
		{
			throw new Exception('The configuration option \''.$name.'\' does not exist.');
		}
		return $this->_opts[$name];
	} // end get();

	/**
	 * Sets the configuration option value.
	 *
	 * @param string $name Option name
	 * @param mixed $value Option value
	 */
	public function set($name, $value)
	{
		$this->_opts[$name] = $value;
	} // end set();

	public function setResolve($name, $value)
	{
		if(is_array($value))
		{
			foreach($value as &$subitem)
			{
				$subitem = $this->_resolve($subitem);
			}
			$this->_opts[$name] = $value;
		}
		else
		{
			$this->_opts[$name] = $this->_resolve($value);
		}
	} // end setResolve();

	public function isDefined($name)
	{
		return isset($this->_opts[$name]);
	} // end isDefined();

	public function merge(array $newOptions, $resolve = true)
	{
		if($resolve)
		{
			foreach($newOptions as $name => $value)
			{
				$this->setResolve($name, $value);
			}
		}
		else
		{
			$this->_opts = array_merge($this->_opts, $newOptions);
		}
	} // end merge();

	private function _resolve($value)
	{
		if(preg_match('/%([a-zA-Z0-9\-\.]+)%/', $value, $matches))
		{
			return str_replace($matches[0], $this->get($matches[1]), $value);
		}
		return $value;
	} // end _resolve();
} // end Config;