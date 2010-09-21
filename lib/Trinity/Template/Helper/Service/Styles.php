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
namespace Trinity\Template\Helper\Service;
use \Trinity\Basement\Service as Basement_Service;
use \Trinity\Template\Helper\Styles as Helper_Styles;
use \Symfony\Component\EventDispatcher\Event;

/**
 * Launches the stylesheet helper.
 *
 * @author Amadeusz "megawebmaster" Starzykiewicz
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
class Styles extends Basement_Service
{
	/**
	 * List of services to preload.
	 * @return array
	 */
	public function toPreload()
	{
		return array('utils.Config');
	} // end toPreload();

	/**
	 * Builds the layout object.
	 */
	public function getObject()
	{
		$config = $this->_serviceLocator->get('utils.Config');
		$styles = new Helper_Styles();

		// Configure helper
		$styles->setBaseUrl($config->baseUrl);
		$styles->setCacheDirectory($config->helpers->styles->cacheDirectory);
		$styles->setMinify($config->helpers->styles->minify);
		$styles->set('gzip_contents', $config->helpers->styles->gzip);
		$eventDispatcher = $this->_serviceLocator->getEventDispatcher();
		$eventDispatcher->connect('template.layout.render', function(Event $event) use($styles)
		{
			$layout = $event->getSubject();
			$layout->getLayout()->assign('helpers', array_merge(
				isset($layout->getLayout()->helpers)?$layout->getLayout()->helpers:array(),
				array('styles' => $styles->getStyles())
			));
		});

		return $styles;
	} // end getObject();
} // end Styles;