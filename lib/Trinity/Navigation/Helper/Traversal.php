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
namespace Trinity\Navigation\Helper;
use \Iterator;
use \Trinity\Navigation\Manager;
use \Trinity\Navigation\Page;

/**
 * The base class for the navigation helpers.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
abstract class Traversal
{
	protected $_renderClass;
	protected $_manager;

	public function __construct(Manager $manager, $renderClass)
	{
		$this->_manager = $manager;
		$this->_renderClass = (string)$renderClass;
	} // end __construct();


	public function getRenderClass()
	{
		return $this->_renderClass;
	} // end getRenderClass();

	/**
	 * Checks, if we can render the page within the given rendering class.
	 *
	 * @param Page $page The page to check
	 * @param Page $parent Validate parent, too?
	 * @return boolean
	 */
	protected function _canRender(Page $page, Page $parent = null)
	{
		switch($page->getRenderClass($this->_renderClass))
		{
			case Page::RENDER_ALWAYS:
				return true;
			case Page::RENDER_ON_ACTIVE:
				if($page->onActivePath || $page->active)
				{
					return true;
				}
				return false;
			case Page::RENDER_ON_INACTIVE:
				if($page->onActivePath || $page->active)
				{
					return false;
				}
				return true;
			case Page::RENDER_NEVER:
				return false;
			case Page::RENDER_SELECTION_SPECIFIC:
				if($parent !== null)
				{
					if($parent->active)
					{
						return true;
					}
				}
				return false;
			case Page::RENDER_IF_PARENT:
				if($parent !== null)
				{
					if($this->_canRender($parent, $parent->getParent()))
					{
						return true;
					}
				}
				return false;
			case Page::RENDER_CHILDREN:
				return false;
		}
	} // end _canRender();
} // end Traversal;