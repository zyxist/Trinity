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

namespace Trinity\Cache;

/**
 * The abstract caching class that provides the basic interface for accessing
 * the cache. The interface is based on that from Symfony 1.x.
 *
 * @author Tomasz Jędrzejewski
 * @author Fabien Potencier <fabien.potencier@symfony-project.com>
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
abstract class Cache
{
	const ALL = 0;
	const OLD = 1;
	const SEPARATOR = ':';

	/**
	 * The cache options.
	 * @var array
	 */
	protected $_options;

	/**
	 * Constructs the caching object.
	 *
	 * @param array $options The caching options.
	 */
	public function __construct(array $options)
	{
		$this->_options = $options;
	} // end __construct();

	/**
	 * Returns the given caching option. If the option is not defined,
	 * an exception is thrown.
	 *
	 * @throws DomainException
	 * @param string $name Option name
	 * @return mixed
	 */
	public function getOption($name)
	{
		if(!isset($this->_options[$name]))
		{
			throw new \DomainException('Unknown cache option: \''.$name.'\'');
		}
		return $this->_options[$name];
	} // end getOption();

	/**
	 * Returns true, if the given caching option is defined.
	 *
	 * @param string $name The option name.
	 * @return boolean
	 */
	public function hasOption($name)
	{
		return isset($this->_options[$name]);
	} // end hasOption();

	/**
	 * Retrieves the given cached object from the cache. If the object is
	 * empty, the default value is returned.
	 *
	 * @param string $key The cache object key
	 * @param mixed $default The default value
	 * @return mixed
	 */
	abstract public function get($key, $default = null);

	/**
	 * Returns true, if the object with the given key is available.
	 *
	 * @param string $key The cache key.
	 * @return boolean
	 */
	abstract public function has($key);

	/**
	 * Creates a new cached object with the given key and lifetime.
	 *
	 * @param string $key The object key
	 * @param mixed $data The cached data
	 * @param int $lifetime The lifetime in seconds.
	 */
	abstract public function set($key, $data, $lifetime = null);

	/**
	 * Removes the object with the given key.
	 *
	 * @param string $key The cache object key.
	 */
	abstract public function remove($key);

	/**
	 * Removes all the objects whose keys match the given pattern.
	 *
	 * @param string $key The key pattern.
	 */
	abstract public function removePattern($key);

	/**
	 * Cleans up the cache.
	 *
	 * @param int $mode The cleaning mode.
	 */
	abstract public function clean($mode);

	/**
	 * Returns the last modification time for the given cache key.
	 *
	 * @param string $key The cache object key.
	 * @return int
	 */
	abstract public function getLastModified($key);

	/**
	 * Converts the cache key pattern to a regular expression.
	 *
	 * @param string $pattern The cache key pattern
	 * @return string
	 */
	protected function _patternToRegexp($pattern)
	{
		$regexp = str_replace(
			array('\\*\\*', '\\*'),
			array('.+?',    '[^'.preg_quote(sfCache::SEPARATOR, '#').']+'),
			preg_quote($pattern, '#')
		);

		return '#^'.$regexp.'$#';
	} // end _patternToRegexp();
} // end Cache;