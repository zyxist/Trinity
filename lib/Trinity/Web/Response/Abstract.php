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
abstract class Response_Abstract
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
	 * If set to true, an exception is throw if the headers
	 * have already been sent.
	 * @var boolean
	 */
	public $throwExceptionsOnHeadersSent = true;

	/**
	 * Sets a HTTP header with the specified value.
	 *
	 * @throws Response_Exception
	 * @param string $name The header name
	 * @param string $value The header value
	 * @param boolean $replace Replace the header, if already exists?
	 * @return Response_Abstract Fluent interface.
	 */
	public function setHeader($name, $value, $replace = true)
	{
		$this->_verifyHeadersSent($name);

		if(isset($this->_headers[$name]) && !$replace)
		{
			throw new Response_Exception('Cannot replace the header '.$name.' - already set.');
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
	 * @param string $name The header name
	 * @return string
	 */
	public function getHeader($name)
	{
		if(!isset($this->_headers[$name]))
		{
			throw new Response_Exception('The header '.$name.' is not defined.');
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
	 * @return Response_Abstract Fluent interface.
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
	 * Sets the response code for this response.
	 * 
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
			throw new Response_Exception('Headers have already been sent.');
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
		echo $this->_body;
	} // end sendBody();

	/**
	 * Sends the entire response.
	 */
	public function sendResponse()
	{
		$this->sendHeaders();
		$this->sendBody();
	} // end sendResponse();

	/**
	 * Throws an exception, if the headers have been sent and the appropriate
	 * option is set.
	 *
	 * @throws \Trinity\Web\Response_Exception
	 * @param string $headerName The header name for the informatory purposes
	 */
	private function _verifyHeadersSent($headerName)
	{
		if($this->_headersSent && $this->throwExceptionsOnHeadersSent)
		{
			throw new Response_Exception('Cannot set '.$headerName.' - headers have already been sent.');
		}
	} // end _verifyHeadersSent();
} // end Response_Abstract;