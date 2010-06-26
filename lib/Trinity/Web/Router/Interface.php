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

namespace Trinity\Web;

/**
 * The router interface. Note that it is not required to perform the
 * route discovery.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
interface Router_Interface
{
	/**
	 * Constructs a path from the variable list.
	 *
	 * @param array $vars The route variables.
	 * @return string
	 */
	public function assemble(array $vars, $area = null);

	/**
	 * Matches the path to the routing rules and extracts the arguments.
	 *
	 * @param string $path The path to route.
	 * @return array
	 */
	public function route($path);

	/**
	 * Sets the predefined router var.
	 *
	 * @param string $name The variable name
	 * @param mixed $value The variable value.
	 */
	public function setParam($name, $value);

	/**
	 * Sets the predefined router variables.
	 *
	 * @param mixed $variables The list of router variables.
	 */
	public function setParams(array $variables);
} // end Router_Interface;