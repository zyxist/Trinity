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
 * Navigation hooks are used for dynamic construction for the parts of
 * the navigation tree. They can either fill the data about the specified
 * page, using the information from other models, or build the list of child
 * pages.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
interface Hook
{
	/**
	 * Adds the information about the specified page.
	 *
	 * @param Page $page The page to fill.
	 */
	public function createPageInfo(Page $page);

	/**
	 * Adds the children for the specified page.
	 *
	 * @param Page $page The page to modify.
	 */
	public function createPageChildren(Page $page);
} // end Hook;