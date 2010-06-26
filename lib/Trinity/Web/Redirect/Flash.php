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

	public function __construct($route, $message)
	{
		parent::__construct($router, 303, $message);
	} // end __construct();
} // end Redirect_Flash;