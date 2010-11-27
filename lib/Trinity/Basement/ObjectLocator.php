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
namespace Trinity\Basement;

/**
 * The basic object locator for the framework that can be freely extended
 * to match programmer's needs.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
class ObjectLocator extends Locator
{
	protected $_creatorFunc;

	/**
	 * Creates the locator.
	 *
	 * @param string $name The locator name
	 */
	public function __construct($name, $creatorFunc)
	{
		parent::__construct($name);
		if(!\is_callable($creatorFunc))
		{
			throw new Exception('The specified creator function is not callable.');
		}
		$this->_creatorFunc = $creatorFunc;
	} // end __construct();
	/**
	 * The action fired when an object is missing. The default implementation
	 * always throws an exception.
	 *
	 * @throws Core_Exception
	 * @param string $name The missing object name.
	 */
	protected function _objectMissing($name)
	{
		$this->_pool[(string)$name] = \call_user_func($this->_creatorFunc, $name);
	} // end _objectMissing();
} // end ObjectLocator;