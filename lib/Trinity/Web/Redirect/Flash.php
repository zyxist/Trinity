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

class Redirect_Flash extends Redirect_Exception
{
	private $_type;

	public function __construct($route, $message, $type = null)
	{
		parent::__construct($route, 303, $message);
		$this->_type = $type;
	} // end __construct();

	public function getType()
	{
		return $this->_type;
	} // end getType();
} // end Redirect_Flash;