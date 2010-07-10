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
use \Trinity\Web\Controller\Manager;
use \Trinity\Web\Controller\State;
use \Trinity\Web\View;

/**
 * Bricks are elementary controller functional units. They are a universal way
 * to provide modularization and functionality composition by controllers.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
abstract class Brick
{
	/**
	 * The controller manager.
	 * @var \Trinity\Web\Controller\Manager
	 */
	private $_manager;

	/**
	 * The controller state.
	 * @var \Trinity\Web\Controller\State
	 */
	private $_state = null;

	public function __construct(Manager $manager)
	{
		$this->_manager = $manager;
	} // end __construct();

	public function setState(State $state)
	{
		$this->_state = $state;
	} // end setState();
	
	/**
	 * Returns the current controller state object.
	 */
	public function getState()
	{
		return $this->_state;
	} // end getState();

	/**
	 * Dispatches the brick.
	 */
	public function dispatch()
	{
		$view = $this->_dispatch($this->_manager);
		if($view instanceof View)
		{
			$this->_manager->processView($view);
		}
	} // end dispatch();

	abstract protected function _dispatch(Manager $manager);
} // end Brick;