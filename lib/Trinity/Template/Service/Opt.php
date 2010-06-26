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
namespace Trinity\Template;
use \Trinity\Basement\Service as Service;
use \Trinity\Basement\Application as BaseApplication;
use \Opt_Class;

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

		// Create the OPT instance.
		$opt = new Opt_Class;
		$opt->compileDir = $this->compileDir;
		$opt->sourceDir = array(
			'file' => $this->appTemplates,
			'app.templates' => $this->appTemplates,
			'app.layouts' => $this->appLayouts,
			'area.templates' => $area->getFilePath('templates'),
			'area.layouts' => $area->getFilePath('layouts')
		);

		$options = $this->getOptions();
		if(!isset($options['stripWhitespaces']))
		{
			$options['stripWhitespaces'] = false;
		}
		$opt->loadConfig($options);

		// Register helpers.
		$opt->register(Opt_Class::PHP_FUNCTION, 'baseUrl', '\Trinity\Template\Helper_Url::baseUrl');
		$opt->register(Opt_Class::PHP_FUNCTION, 'url', '\Trinity\Template\Helper_Url::url');

		$eventManager = BaseApplication::getApplication()->getEventManager();
		$serviceLocator = $this->_serviceLocator;
		$eventManager->addCallback('template.layout.configure', function($args) use($serviceLocator)
		{
			$session = $serviceLocator->get('web.Session');
			\Opt_View::assignGlobal('flash', $session->getNamespace('flash'));
			\Opt_View::setFormatGlobal('flash', 'Global/Objective', false);
		});

		return $opt;
	} // end getObject();
} // end Service_Opt;