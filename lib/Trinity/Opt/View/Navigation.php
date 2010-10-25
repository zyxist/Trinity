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

namespace Trinity\Opt\View;
use \Trinity\Basement\ServiceLocator;
use \Trinity\Navigation\Helper\ActivePath;

/**
 * This is a facade helper for the navigation.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
class Navigation extends Html
{
	/**
	 * Dispatches the view.
	 */
	public function dispatch()
	{
		$layout = $this->_serviceLocator->get('Layout');
		$layoutTemplate = $layout->getLayout();

		$navigation = $this->getModel('navigation', '\\Trinity\\Navigation\\Manager');

		$layoutTemplate->title = new ActivePath($navigation, 'title');
		$layoutTemplate->breadcrumbs = new ActivePath($navigation, 'breadcrumbs');
	//		'menu' => $this->_serviceLocator->get('MenuHelper'),

		$layoutTemplate->setFormat('title', 'ActivePath/NavigationPage');
		$layoutTemplate->setFormat('breadcrumbs', 'ActivePath/NavigationPage');
	//	$layoutTemplate->setFormat('menu', 'MenuHelper');
	} // end dispatch();
} // end Navigation;