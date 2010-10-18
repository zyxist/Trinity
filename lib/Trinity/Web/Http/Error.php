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
class Error extends \Exception
{
	const BAD_REQUEST = 400;
	const UNAUTHORIZED = 401;
	const PAYMENT_REQUIRED = 402;
	const FORBIDDEN = 403;
	const NOT_FOUND = 404;
	const METHOD_NOT_ALLOWED = 405;
	const NOT_ACCEPTABLE = 406;
	const GONE = 410;
	const PRECONDITION_FAILED = 412;

	const NOT_IMPLEMENTED = 501;

	/**
	 * Constructs the HTTP error exception. By convention, exception code =
	 * error code.
	 *
	 * @param string $message The error message.
	 * @param int $responseCode The response code.
	 */
	public function __construct($message, $responseCode, $previous = null)
	{
		parent::__construct($message, (int)$responseCode);
	} // end __construct();
} // end Exception;