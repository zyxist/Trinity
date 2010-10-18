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

namespace Trinity\Web\Http;

/**
 * This exception is used to perform HTTP redirections.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
class Redirect extends \Exception
{
	const MULTIPLE_CHOICES = 300;
	const MOVED_PERMANENTLY = 301;
	const FOUND = 302;
	const SEE_OTHER = 303;
	const NOT_MODIFIED = 304;
	const USE_PROXY = 305;
	const TEMPORARY_REDIRECT = 307;

	/**
	 * Constructs the HTTP redirection object.
	 *
	 * @param string $message The redirection address.
	 * @param int $responseCode The redirection type
	 */
	public function __construct($route, $responseCode = 303, $previous = null)
	{
		parent::__construct($route, (int)$responseCode, $previous);
	} // end __construct();
} // end Redirect;