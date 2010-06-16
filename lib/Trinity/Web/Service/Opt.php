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
use \Trinity\Basement\Service as Service;

/**
 * The Open Power Template instance builder.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
class Service_Opt extends Service
{
	/**
	 * List of services to preload.
	 * @return array
	 */
	public function toPreload()
	{
		return array('web.Area');
	} // end toPreload();

	/**
	 * Preconfigures and initializes the OPT object.
	 *
	 * @return \Opt_Class
	 */
	public function getObject()
	{
		$area = $this->_serviceLocator->get('web.Area');

		$opt = new \Opt_Class;
		$opt->compileDir = $this->compileDir;
		$opt->sourceDir = array(
			'file' => $this->appTemplates,
			'app.templates' => $this->appTemplates,
			'app.layouts' => $this->appLayouts,
			'area.templates' => $area->getFilePath('templates'),
			'area.layouts' => $area->getFilePath('layouts')
		);

		return $opt;
	} // end getObject();
} // end Service_Opt;