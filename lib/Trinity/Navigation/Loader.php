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
 * Navigation loaders read the tree structure from an external source.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
interface Loader
{
	/**
	 * Returns the tree identifier for the cache.
	 * 
	 * @return string
	 */
	public function getIdentifier();

	/**
	 * This method should build and return the root of the navigation tree.
	 *
	 * @return \Trinity\Navigation\Page
	 */
	public function buildNavigationTree();
} // end Loader;