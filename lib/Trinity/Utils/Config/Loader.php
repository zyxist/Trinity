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

namespace Trinity\Utils\Config;

/**
 * Interface for configuration loaders.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
interface Loader
{
	/**
	 * The read options are returned as a plain list that must be converted
	 * by the configuration object for a tree.
	 */
	const PLAIN_LIST = 0;
	/**
	 * The option list is returned in a nested form.
	 */
	const NESTED_LIST = 1;

	/**
	 * Loads the configuration for the specified environment.
	 *
	 * @param string $environment The environment name.
	 * @return array
	 */
	public function loadConfig($environment);

	/**
	 * Returns the type of the output returned by loadConfig() in order
	 * to provide a proper deploying strategy.
	 *
	 * @return int
	 */
	public function getOutputType();
} // end Loader;