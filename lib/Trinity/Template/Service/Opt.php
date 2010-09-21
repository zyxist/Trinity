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
use \Symfony\Component\EventDispatcher\Event;
use \Trinity\Basement\Service as Basement_Service;
use \Trinity\Basement\Application as BaseApplication;
use \Opt_Class;

/**
 * The Open Power Template instance builder.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
class Opt extends Basement_Service
{
	/**
	 * List of services to preload.
	 * @return array
	 */
	public function toPreload()
	{
		return array();
		//return array('web.Area');
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
			'module.templates' => $area->getPrimaryModule()->getFilePath('templates'),
			'module.layouts' => $area->getPrimaryModule()->getFilePath('layouts'),
			'area.templates' => $area->getFilePath('templates'),
			'area.layouts' => $area->getFilePath('layouts')
		);

		// Load URL helper
		$serviceLocator = $this->_serviceLocator;
		$urlHelper = $serviceLocator->get('helper.Url');
		// Register URL helper instruction.
		$opt->register(Opt_Class::OPT_NAMESPACE, 'trinity');
		$opt->register(Opt_Class::OPT_INSTRUCTION, 'Url', '\Trinity\Template\Helper\Instruction\Url');
		
		$options = $this->getOptions();
		if(!isset($options['stripWhitespaces']))
		{
			$options['stripWhitespaces'] = false;
		}
		$opt->loadConfig($options);

		$eventDispatcher = $serviceLocator->getEventDispatcher();
		$eventDispatcher->connect('template.layout.configure', function(Event $event) use($serviceLocator)
		{
			$session = $serviceLocator->get('web.Session');
			\Opt_View::assignGlobal('flash', $session->getNamespace('flash'));
			\Opt_View::setFormatGlobal('flash', 'Global/Objective', false);
		});

		return $opt;
	} // end getObject();
} // end Opt;