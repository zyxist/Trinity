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
namespace Trinity\Navigation;

/**
 * The navigation manager is the entry point to the navigation subsystem.
 * It keeps the navigation tree, and provides the necessary knowledge
 * and discovery for the current location within it.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
class Manager
{
	/**
	 * The root of the navigation tree.
	 * @var \Trinity\Navigation\Page
	 */
	protected $_tree;

	/**
	 * The reader object.
	 * @var \Trinity\Navigation\Reader
	 */
	protected $_reader;

	/**
	 * Sets the reader responsible for loading the tree structure. Implements
	 * fluent interface.
	 * 
	 * @param Reader $reader The reader object or the reader class name.
	 * @return \Trinity\Navigation\Manager
	 */
	public function setReader(Reader $reader)
	{
		$this->_reader = $reader;
		return $this;
	} // end setReader();

	/**
	 * Returns the registered reader object.
	 * 
	 * @return Reader
	 */
	public function getReader()
	{
		return $this->_reader;
	} // end getReader();

	public function setCache(Cache $cache)
	{

	} // end setCache();

	public function getCache()
	{

	} // end getCache();

	public function setMatcher(Matcher $cache)
	{

	} // end setMatcher();

	public function getMatcher()
	{

	} // end getMatcher();

	public function addHook($name, $hook)
	{

	} // end addHook();

	public function hasHook($name)
	{

	} // end hasHook();

	public function getHook($name)
	{

	} // end getHook();

	public function getNavigationTree()
	{

	} // end getNavigationTree();

	public function getActivePage()
	{

	} // end getActivePage();

	public function getPageMappings()
	{

	} // end getPageMappings();

	/**
	 * Performs the navigation discovery. Loads the tree structure, matches the
	 * active page, and initializes the navigation manager. Implements fluent
	 * interface.
	 *
	 * @return \Trinity\Navigation\Manager
	 */
	public function discover()
	{

	} // end discover();
} // end Manager;