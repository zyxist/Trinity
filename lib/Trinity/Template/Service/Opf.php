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
namespace Trinity\Template\Service;
use \Trinity\Basement\Service as Basement_Service;
use \Opf_Class;

/**
 * The Open Power Forms instance builder.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
class Opf extends Basement_Service
{
	/**
	 * List of services to preload.
	 * @return array
	 */
	public function toPreload()
	{
		return array('template.Opt');
	} // end toPreload();

	/**
	 * Preconfigures and initializes the OPF object.
	 *
	 * @return \Opt_Class
	 */
	public function getObject()
	{
		// Create the OPF instance.
		return new Opf_Class($this->_serviceLocator->get('template.Opt'));
	} // end getObject();
} // end Opf;