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
namespace Trinity\Opt;
use \Opt_Class;
use \Symfony\Component\EventDispatcher\Event;
use \Trinity\Basement\Service\Container;
use \Trinity\Basement\ServiceLocator;

/**
 * Defines, how to start the default web stack services and configure them.
 * These services may be overwritten by other containers.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
class Services extends Container
{
	public function getConfiguration()
	{
		return array(
			'trinity.opt.layout' => 'application.layouts:layout',
			'trinity.opt.stripWhitespaces' => false,
			'trinity.opt.parser' => 'Opt_Parser_Xml',
			'trinity.opt.escape' => true,
			'trinity.opt.prologRequired' => true,
			'trinity.opt.compileMode' => 0,
		);
	} // end getConfiguration();

	public function getOptService(ServiceLocator $serviceLocator)
	{
		$areaManager = $serviceLocator->get('AreaManager');
		$application = $serviceLocator->get('Application');

		$area = $areaManager->getActiveArea();
		$module = $areaManager->getActiveModule();

		// Create the OPT instance.
		$opt = new Opt_Class;
		$opt->compileDir = $application->getDirectory().'cache'.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR;
		$opt->setInflector($serviceLocator->get('Inflector'));

		$config = $serviceLocator->getConfiguration();

		$opt->loadConfig(array(
			'stripWhitespaces' => $config->get('trinity.opt.stripWhitespaces'),
			'parser' => $config->get('trinity.opt.parser'),
			'escape' => $config->get('trinity.opt.escape'),
			'prologRequired' => $config->get('trinity.opt.prologRequired'),
			'compileMode' => $config->get('trinity.opt.compileMode'),
		));

		// Register helpers.
		$opt->register(Opt_Class::PHP_FUNCTION, 'baseUrl', '\Trinity\Opt\Helper_Url::baseUrl');
		$opt->register(Opt_Class::PHP_FUNCTION, 'url', '\Trinity\Opt\Helper_Url::url');
		$opt->register(Opt_Class::OPT_FORMAT, 'Flash', '\Trinity\Opt\Format\Flash');

		$session = $serviceLocator->get('Session');
		\Opt_View::assignGlobal('flash', $serviceLocator->get('FlashHelper'));
		\Opt_View::setFormatGlobal('flash', 'Global/Flash', false);

		$opt->setup();

		return $opt;
	} // end getOptService();

	public function getInflectorService(ServiceLocator $serviceLocator)
	{
		$inflector = new Inflector($serviceLocator->get('Application'));
		
		$areaManager = $serviceLocator->get('AreaManager');

		if($areaManager->getActiveArea() !== null)
		{
			$inflector->setArea($areaManager->getActiveArea());
			$inflector->setModule($areaManager->getActiveModule());
		}
		else
		{
			$eventDispatcher = $serviceLocator->get('EventDispatcher');
			$eventDispatcher->connect('web.application.modules-discovered', function(Event $event) use($inflector)
			{
				$inflector->setModule($event->getParameter('module'));
				$inflector->setArea($event->getParameter('area'));
			});
		}

		return $inflector;
	} // end getInflectorService();

	public function getOpfService(ServiceLocator $serviceLocator)
	{
		return new Opf_Class($serviceLocator->get('Opt'));
	} // end getOpfService();

	public function getLayoutService(ServiceLocator $serviceLocator)
	{
		// It's a kind of magic!
		$opt = $serviceLocator->get('Opt');

		// Create the layout object
		$config = $serviceLocator->getConfiguration();
		$layout = new Layout($serviceLocator->get('EventDispatcher'));
		$layout->setLayout($config->get('trinity.opt.layout'));

		// TODO: Replace with something more clever.
		$response = $serviceLocator->get('Response');
		$response->setHeader('Content-type', 'text/html;charset=utf-8');

		return $layout;
	} // end getLayoutService();
} // end Services;