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
use \SplQueue;
use \Trinity\Cache\Cache;

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
	 * The loader object.
	 * @var \Trinity\Navigation\Loader
	 */
	protected $_loader;

	/**
	 * The caching object.
	 * @var \Trinity\Cache\Cache
	 */

	/**
	 * The list of navigation tree hooks.
	 * @var array
	 */
	protected $_hooks;

	/**
	 * Constructs the area manager.
	 *
	 * @param Cache $cache The caching system.
	 * @param Loader $loader The navigation structure loader.
	 */
	public function __construct(Cache $cache, Loader $loader)
	{
		$this->_cache = $cache;
		$this->_loader = $loader;
	} // end __construct();

	/**
	 * Returns the registered loader object.
	 * 
	 * @return Loader
	 */
	public function getLoader()
	{
		return $this->_loader;
	} // end getReader();

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
		return $this->_activePage;
	} // end getActivePage();

	/**
	 * Performs the navigation tree discovery.
	 *
	 * @return \Trinity\Navigation\Manager
	 */
	public function discover()
	{
		// Build the navigation tree
		if($this->_cache->has('trinity:navigation:'.$this->_loader->getIdentifier()))
		{
			$tree = $this->_cache->get('trinity:navigation:'.$this->_loader->getIdentifier());
		}
		else
		{
			$tree = $this->_loader->buildNavigationTree();
			$this->_cache->set('trinity:navigation:'.$this->_loader->getIdentifier(), $tree);
		}
		
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
			$hookName = $item->hook;
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
		return $this;
	} // end discover();

	/**
	 * Finds the active page, using the request and controller data.
	 *
	 * @param string $controller The name of the controller that handles the request.
	 * @param array $data The controller and request data.
	 * @return Page
	 */
	public function findActivePage($controller, array $data)
	{
		if($this->_activePage !== null)
		{
			return $this->_activePage;
		}
		if($this->_tree === null)
		{
			$this->discover();
		}

		$queue = new SplQueue;
		$queue->enqueue($this->_tree);
		while($queue->count() > 0)
		{
			$item = $queue->dequeue();
			if($item->getPageType() == Page::TYPE_REAL)
			{
				if($item->controller == $controller)
				{
					$ok = true;
					foreach($data as $name => $value)
					{
						if($item->$name != $value)
						{
							$ok = false;
							break;
						}
					}
					if($ok)
					{
						$this->setActivePage($item);
						return $item;
					}
				}
			}
			foreach($item as $subitem)
			{
				$queue->enqueue($subitem);
			}
		}
		return null;
	} // end findActivePage();

	/**
	 * Selects the active page.
	 * 
	 * @param Page $page The active page.
	 * @return Manager 
	 */
	public function setActivePage(Page $page)
	{
		if($this->_tree === null)
		{
			$this->discover();
		}

		// Mark all the pages on the way to the root
		$item = $page->getParent();
		$page->active = true;
		while($item !== null)
		{
			$page->onActivePath = true;
			$parent = $item->getParent();

			if($parent === null && $item !== $this->_tree)
			{
				throw new Exception('The selected active page is not a part of the navigation tree.');
			}
			$item = $parent;
		}
		$this->_activePage = $page;

		return $this;
	} // end setActivePage();
} // end Manager;