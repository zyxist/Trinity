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

use \Trinity\Basement\View as Basement_View;
use \Trinity\Basement\ServiceLocator;
use \Trinity\Web\View\Broker as View_Broker;

/**
 * The base interface for web views.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
abstract class View extends Basement_View
{
	/**
	 * The service locator link.
	 * @var \Trinity\Basement\ServiceLocator
	 */
	protected $_serviceLocator;

	/**
	 * Constructs the view.
	 * 
	 * @param \Trinity\Basement\ServiceLocator $serviceLocator The service locator
	 */
	public function __construct(ServiceLocator $serviceLocator)
	{
		$this->_serviceLocator = $serviceLocator;
	} // end __construct();

	/**
	 * This method should return the view broker it is supposed to work
	 * with (i.e. layout manager).
	 *
	 * @return View_Broker
	 */
	abstract public function getViewBroker();

	/**
	 * This method is called, if the broker is already selected. It is passed
	 * to the view, so that it can check if it is designed to work with it
	 * and send there the results.
	 *
	 * @param View_Broker $broker The view broker.
	 */
	abstract public function setViewBroker(View_Broker $broker);

	/**
	 * Dispatches the view.
	 */
	abstract public function dispatch();
} // end View;