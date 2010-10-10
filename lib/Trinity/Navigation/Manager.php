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
	 * The active page.
	 * @var \Trinity\Navigation\Page
	 */
	protected $_activePage;

	/**
	 * The reader object.
	 * @var \Trinity\Navigation\Reader
	 */
	protected $_reader;

	/**
	 * The matcher object.
	 * @var \Trinity\Navigation\Matcher
	 */
	protected $_matcher;

	/**
	 * The list of navigation tree hooks.
	 * @var array
	 */
	protected $_hooks;

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

	public function setMatcher(Matcher $matcher)
	{
		$this->_matcher = $matcher;
		return $this;
	} // end setMatcher();

	public function getMatcher()
	{
		return $this->_matcher;
	} // end getMatcher();

	public function addHook($name, Hook $hook)
	{
		if(isset($this->_hooks[(string)$name]))
		{
			throw new Exception('The specified navigation hook name: \''.$name.'\' is already in use.');
		}
		$this->_hooks[(string)$name] = $hook;
		return $this;
	} // end addHook();

	public function hasHook($name)
	{
		return $this->_hooks[(string)$name];
	} // end hasHook();

	public function getHook($name)
	{
		if(!isset($this->_hooks[(string)$name]))
		{
			throw new Exception('The specified navigation hook \''.$name.'\' does not exist.');
		}
		return $this->_hooks[(string)$name];
	} // end getHook();

	/**
	 * Returns the root of the navigation tree.
	 * @return \Trinity\Navigation\Page
	 */
	public function getNavigationTree()
	{
		return $this->_tree;
	} // end getNavigationTree();

	/**
	 * Returns the active page.
	 * @return \Trinity\Navigation\Page
	 */
	public function getActivePage()
	{
		return $this->_active;
	} // end getActivePage();

	/**
	 * Performs the navigation discovery. Loads the tree structure, matches the
	 * active page, and initializes the navigation manager. Implements fluent
	 * interface.
	 *
	 * @return \Trinity\Navigation\Manager
	 */
	public function discover()
	{
		// Build the navigation tree
		$tree = $this->_reader->buildNavigationTree();
		if(!$tree instanceof Page)
		{
			throw new Exception('The navigation reader returned an invalid tree: object of class \Trinity\Navigation\Page was expected.');
		}
		$this->_tree = $tree;

		// Match the hooks to the pages.
		$queue = new SplQueue;
		$queue->enqueue($tree);
		do
		{
			$item = $queue->dequeue();
			$hookName = $item->getHookName();
			if($hookName !== null)
			{
				$item->setHook($this->getHook($hookName));
			}
			foreach($item as $subitem)
			{
				$queue->enqueue($subitem);
			}
		}
		while($queue->count() > 0);

		// Discover the active page.
		$page = $this->_matcher->matchPage($tree);
		if(is_object($page) && !$page instanceof Page)
		{
			throw new Exception('The navigation matcher returned an invalid active page: object of class \Trinity\Navigation\Page was expected.');
		}

		// Mark all the pages on the way to the root
		$item = $page->getParent();
		$page->setFlags($page->getFlags() | Page::ACTIVE);
		while($item !== null)
		{
			$page->setFlags($page->getFlags() | Page::ON_ACTIVE_PATH);
			$parent = $item->getParent();

			if($parent === null && $item !== $tree)
			{
				throw new Exception('The selected active page is not a part of the navigation tree.');
			}
			$item = $parent;
		}

		$this->_activePage = $page;

		return $this;
	} // end discover();
} // end Manager;