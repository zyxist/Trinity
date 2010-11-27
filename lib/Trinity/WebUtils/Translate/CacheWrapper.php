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
namespace Trinity\WebUtils\Translate;
use \Opc\Translate\CachingInterface;
use \Trinity\Cache\Cache;

/**
 * A wrapper that allows to cache translation messages.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
class CacheWrapper implements CachingInterface
{
	/**
	 * The cache adapter.
	 * @var \Trinity\Cache\Cache
	 */
	private $_cache;

	/**
	 * Creates the wrapper for the given cache adapter.
	 *
	 * @param Cache $cache Cache adapter.
	 */
	public function __construct(Cache $cache)
	{
		$this->_cache = $cache;
	} // end __cache();

	public function hasGroup($language, $group)
	{
		return $this->_cache->has('trinity:translation:'.$language.':'.$group);
	} // end hasGroup();

	public function getGroup($language, $group)
	{
		return $this->_cache->get('trinity:translation:'.$language.':'.$group);
	} // end getGroup();

	public function setGroup($language, $group, array $data)
	{
		return $this->_cache->set('trinity:translation:'.$language.':'.$group, $data);
	} // end setGroup();
} // end CacheWrapper;