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
 * The APC cache implementation, based on that from Symfony 1.x.
 *
 * @author Tomasz Jędrzejewski
 * @author Fabien Potencier <fabien.potencier@symfony-project.com>
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
class APC extends Cache
{
	/**
	 * Creates the APC caching object. If APC is not enabled on this web server,
	 * an exception is thrown.
	 *
	 * @throws \LogicException
	 * @param array $options APC options
	 */
	public function __construct(array $options)
	{
		parent::__construct($options);

		if(!extension_loaded('apc') || !ini_get('apc.enabled'))
		{
			throw new \LogicException('Cannot initialize APC cache: not supported by the server.');
		}
	} // end __construct();

	public function get($key, $default = null)
	{
		$success = null;
		$value = apc_fetch($this->getOption('prefix').$key, $success);
		return ($success ? $value : $default);
	} // end get();

	public function has($key)
	{
		$success = null;
		apc_fetch($this->getOption('prefix').$key, $success);
		return $success;
	} // end has();

	public function set($key, $data, $lifetime = null)
	{
		if($lifetime === null)
		{
			$lifetime = $this->getOption('lifetime');
		}
		return apc_store($this->getOption('prefix').$key, $data, $lifetime);
	} // end set();

	public function remove($key)
	{
		return apc_delete($this->getOption('prefix').$key);
	} // end remove();

	public function removePattern($pattern)
	{
		$infos = apc_cache_info('user');
		if(!is_array($infos))
		{
			return ;
		}
		$regexp = $this->_patternToRegexp($this->getOption('prefix').$pattern);
		foreach($infos['cache_list'] as $info)
		{
			if(preg_match($regexp, $info['info']))
			{
				apc_delete($info['info']);
			}
		}
	} // end removePattern();

	public function clean($mode)
	{
		if($mode === Cache::ALL)
		{
			return apc_clear_cache('user');
		}
	} // end clean();

	public function getLastModified($key)
	{

	} // end getLastModified();
} // end APC;