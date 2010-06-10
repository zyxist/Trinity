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
 *
 * $Id$
 */

namespace Trinity\Garage\Config;

interface Loader
{
	const PLAIN_LIST = 0;
	const NESTED_LIST = 1;

	public function loadConfig($environment);
	public function getOutputType();
} // end Loader;