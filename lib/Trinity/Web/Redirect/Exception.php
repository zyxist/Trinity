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

/**
 * This exception is used to perform HTTP redirections.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
class Redirect_Exception extends \Exception
{
	const MULTIPLE_CHOICES = 300;
	const MOVED_PERMANENTLY = 301;
	const FOUND = 302;
	const SEE_OTHER = 303;
	const NOT_MODIFIED = 304;
	const USE_PROXY = 305;
	const TEMPORARY_REDIRECT = 307;

	/**
	 * The redirection route. If specified as a string, it must be
	 * an absolute URL.
	 *
	 * @var string|array
	 */
	private $_route;

	/**
	 * Constructs a redirection exception. It should be captured by the controller
	 * which should redirect the user to the specified location. The route can
	 * be either an absolute URI or a list of arguments for the router.
	 * 
	 * @param string|array $route The route to follow.
	 * @param int $responseCode The optional response code.
	 */
	public function __construct($route, $responseCode = 303, $message = null)
	{
		parent::__construct($message, (int)$responseCode);
		$this->_route = $route;
	} // end __construct();

	/**
	 * Returns the route to follow.
	 *
	 * @return string|array
	 */
	public function getRoute()
	{
		return $this->_route;
	} // end getRoute();
} // end Redirect_Exception;