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
 * The abstract HTTP response class responsible for flushing the output
 * and managing headers.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
class Response
{
	/**
	 * The HTTP response code.
	 * @var int
	 */
	private $_responseCode = 200;

	/**
	 * The response body
	 */
	private $_body = '';

	/**
	 * The list of HTTP headers
	 * @var array
	 */
	private $_headers = array();

	/**
	 * The list of raw headers.
	 * @var array
	 */
	private $_rawHeaders = array();

	/**
	 * Are the headers sent?
	 * @var boolean
	 */
	private $_headersSent = false;

	/**
	 * Body generator
	 * @var callback
	 */
	private $_bodyGenerator = null;

	/**
	 * If set to true, an exception is throw if the headers
	 * have already been sent.
	 * @var boolean
	 */
	public $throwExceptionsOnHeadersSent = true;

	/**
	 * Sets a HTTP header with the specified value.
	 *
	 * @throws \Trinity\Web\Response\Exception
	 * @param string $name The header name
	 * @param string $value The header value
	 * @param boolean $replace Replace the header, if already exists?
	 * @return Response Fluent interface.
	 */
	public function setHeader($name, $value, $replace = true)
	{
		$this->_verifyHeadersSent($name);

		if(isset($this->_headers[$name]) && !$replace)
		{
			throw new Response\Exception('Cannot replace the header '.$name.' - already set.');
		}

		$this->_headers[$name] = array(
			'value' => (string)$value,
			'replace' => $replace
		);
		return $this;
	} // end setHeader();

	/**
	 * Returns the header value. If the header does not exist, an exception
	 * is thrown.
	 *
	 * @throws \Trinity\Web\Response\Exception
	 * @param string $name The header name
	 * @return string
	 */
	public function getHeader($name)
	{
		if(!isset($this->_headers[$name]))
		{
			throw new Response\Exception('The header '.$name.' is not defined.');
		}
		return $this->_headers[$name]['value'];
	} // end getHeader();

	/**
	 * Checks if the specified header is set.
	 *
	 * @param string $name The header name
	 * @return boolean
	 */
	public function hasHeader($name)
	{
		return isset($this->_headers[$name]);
	} // end hasHeader();

	/**
	 * Removes the header with the specified name.
	 *
	 * @param string $name The header name
	 * @return Response Fluent interface.
	 */
	public function removeHeader($name)
	{
		if(isset($this->_headers[$name]))
		{
			unset($this->_headers[$name]);
		}

		return $this;
	} // end removeHeader();

	/**
	 * Produces the redirect.
	 *
	 * @param string $url A fully qualified URL
	 * @param int $responseCode The response code.
	 */
	public function setRedirect($url, $responseCode = 302)
	{
		$this->setHeader('Location', $url, true)
			->setResponseCode($responseCode);
	} // end setRedirect();

	/**
	 * Checks, if the response is a redirection.
	 *
	 * @return boolean
	 */
	public function isRedirect()
	{
		return isset($this->_headers['Location']);
	} // end isRedirect();

	/**
	 * Sets the response code for this response.
	 *
	 * @throws \Trinity\Web\Response\Exception
	 * @param int $code The HTTP response code.
	 */
	public function setResponseCode($code)
	{
		if(!is_int($code) || $code < 100 || $code > 599)
		{
			throw new Response_Exception('Invalid HTTP response code: '.$code);
		}

		$this->_responseCode = $code;
	} // end setResponseCode();

	/**
	 * Returns the current response code.
	 * @return integer
	 */
	public function getResponseCode()
	{
		return $this->_responseCode;
	} // end getResponseCode();

	/**
	 * Sets the body generator.
	 *
	 * @throws \Trinity\Web\Response\Exception
	 * @param callback $generator Body generator callback
	 * @return Response Fluent interface.
	 */
	public function setBodyGenerator($generator)
	{
		if(!is_callable($generator))
		{
			throw new Response\Exception('The specified body generator is not a valid callback.');
		}
		$this->_bodyGenerator = $generator;

		return $this;
	} // end setBodyGenerator;

	/**
	 * Returns the current body generator.
	 *
	 * @return callback
	 */
	public function getBodyGenerator()
	{
		return $this->_bodyGenerator;
	} // end getBodyGenerator();

	/**
	 * Sets the HTTP body.
	 * @param string $body The HTTP body
	 */
	public function setBody($body)
	{
		$this->_body = (string)$body;
	} // end setBody();

	/**
	 * Appends a new content to the HTTP body.
	 * @param string $body The body to append
	 */
	public function appendBody($body)
	{
		$this->_body .= (string)$body;
	} // end appendBody();

	/**
	 * Returns the HTTP response body.
	 *
	 * @return string
	 */
	public function getBody()
	{
		return $this->_body;
	} // end getBody();

	/**
	 * Sends the HTTP headers.
	 */
	public function sendHeaders()
	{
		if($this->_headersSent && $this->throwExceptionsOnHeadersSent)
		{
			throw new Response\Exception('Headers have already been sent.');
		}

		// Send the HTTP code
		header('HTTP/1.1 '.$this->_responseCode);

		// Send the headers
		foreach($this->_rawHeaders as $header)
		{
			header($header);
		}

		foreach($this->_headers as $name => $data)
		{
			header($name.': '.$data['value'], $data['replace']);
		}

		$this->_headersSent = true;

	} // end sendHeaders();

	/**
	 * Sends the response body.
	 */
	public function sendBody()
	{
		$this->_headersSent = true;

		if($this->_bodyGenerator === null)
		{
			echo $this->_body;
		}
		else
		{
			call_user_func($this->_bodyGenerator);
		}
	} // end sendBody();

	/**
	 * Sends the entire response.
	 */
	public function sendResponse()
	{
		$this->sendHeaders();
		if(!$this->isRedirect())
		{
			$this->sendBody();
		}
	} // end sendResponse();

	/**
	 * Throws an exception, if the headers have been sent and the appropriate
	 * option is set.
	 *
	 * @throws \Trinity\Web\Response\Exception
	 * @param string $headerName The header name for the informatory purposes
	 */
	private function _verifyHeadersSent($headerName)
	{
		if($this->_headersSent && $this->throwExceptionsOnHeadersSent)
		{
			throw new Response\Exception('Cannot set '.$headerName.' - headers have already been sent.');
		}
	} // end _verifyHeadersSent();
} // end Response;