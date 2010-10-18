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
namespace Trinity\Ops;
use \Trinity\Basement\Module as Basement_Module;

/**
 * Adds Open Power Security support to Trinity.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
class Module extends Basement_Module
{
	/**
	 * Creates the service container for this module.
	 *
	 * @return Services
	 */
	public function registerServiceContainer()
	{
		return new Services;
	} // end registerServiceContainer();
} // end Module;