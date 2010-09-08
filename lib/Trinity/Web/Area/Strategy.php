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
namespace Trinity\Web\Area;

/**
 * The area selection strategy.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
interface Strategy
{
	/**
	 * Returns the information about the specified area.
	 *
	 * @param string $name Area name
	 * @return array
	 */
	public function getAreaOptions($name);

	/**
	 * Discoveries the area and returns the area data in a form of list(name, data).
	 *
	 * @return list(name, data)
	 */
	public function discoverArea();
} // end Strategy_Interface;