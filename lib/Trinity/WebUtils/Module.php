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
namespace Trinity\WebUtils;
use \Trinity\Basement\Module as Basement_Module;

/**
 * The web utilities module adds several default tools useful for building
 * websites, such as default controllers, model stuff and CRUD systems.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
class Module extends Basement_Module
{
	/**
	 * Returns the service container to register.
	 * 
	 * @return Services
	 */
	public function registerServiceContainer()
	{
		return new Services;
	} // end registerServiceContainer();

	/**
	 * Loads the model web utilities.
	 */
	public function launch()
	{
		spl_autoload_call('\\Trinity\\WebUtils\\Model\\Interfaces');
	} // end launch();
} // end Module;