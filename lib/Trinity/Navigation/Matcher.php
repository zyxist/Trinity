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
 * Navigation matchers should be extensions of various web controllers that
 * are able to discover the active page, using the controller arguments.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
interface Matcher
{
	/**
	 * This method should discover the active page using the controller
	 * arguments and return it.
	 *
	 * @return \Trinity\Navigation\Matcher
	 */
	public function matchPage(Manager $manager);
} // end Matcher;