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
namespace Trinity\Navigation\Reader;
use \Trinity\Navigation\Page;
use \Trinity\Navigation\Reader;

/**
 * The basic navigation tree reader implementation. It reads the tree structure
 * from the PHP arrays.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
class Php implements Reader
{
	/**
	 * The navigation tree identifier for caching.
	 * @var string
	 */
	private $_identifier;
	/**
	 * The array description of the navigation tree.
	 * @var array
	 */
	private $_description = null;

	/**
	 * Constructs a new PHP navigation reader which builds a navigation
	 * tree from the specified array.
	 * @param string $identifier The identifier used for caching.
	 * @param array $description The navigation tree description.
	 */
	public function __construct($identifier, array $description)
	{
		$this->_identifier = $identifier;
		$this->_description = $description;
	} // end __construct();

	/**
	 * Returns the navigation tree identifier.
	 *
	 * @return string
	 */
	public function getIdentifier()
	{
		return $this->_identifier;
	} // end getIdentifier();

	/**
	 * Builds a navigation tree from arrays.
	 *
	 * @return \Trinity\Navigation\Page
	 */
	public function buildNavigationTree()
	{
		$queue = new SplQueue;
		$root = $this->_pageFactory($this->_description, $queue);

		while($queue->count() > 0)
		{
			list($parentPage, $pageDesc) = $queue->dequeue();
			$parentPage->appendChild($this->_pageFactory($pageDesc, $queue));
		}

		return $root;
	} // end buildNavigationTree();

	/**
	 * The page factory. Produces a single page object and enqueues the
	 * children descriptions.
	 * 
	 * @param array $pageDesc The array page description.
	 * @param SplQueue $queue The processing queue.
	 * @return Page
	 */
	protected function _pageFactory(array $pageDesc, SplQueue $queue)
	{
		$page = new Page;
		$thereArePages = false;
		foreach($pageDesc as $key => $value)
		{
			if($key !== 'pages')
			{
				$page->$key = $value;
			}
			else
			{
				$thereArePages = true;
			}
		}
		if($thereArePages)
		{
			foreach($pageDesc['pages'] as $subPageDesc)
			{
				$queue->enqueue(array($page, $subPageDesc));
			}
		}

		return $page;
	} // end _pageFactory();
} // end Php;