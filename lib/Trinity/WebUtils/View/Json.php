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
namespace Trinity\WebUtils\View;
use \Trinity\Web\View as Web_View;
use \Trinity\Web\View\Broker;
use \Trinity\Web\View\Exception;
use \Trinity\Web\Request;
use \Trinity\Web\Response;


/**
 * The base interface for JSON views.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
abstract class Json extends Web_View implements Broker
{
	/**
	 * The JSON answer.
	 * @var array
	 */
	private $_answer;

	/**
	 * The response object.
	 * @var \Trinity\Web\Response\Abstract
	 */
	private $_response;

	/**
	 * Returns itself as a view broker.
	 *
	 * @return Json
	 */
	public function getViewBroker()
	{
		return $this;
	} // end getViewBroker();

	/**
	 * Throws an exception - cannot work with another view broker.
	 *
	 * @throws \Trinity\Web\View\Exception
	 * @param View_Broker $broker The view broker to install.
	 */
	public function setViewBroker(Broker $broker)
	{
		throw new Exception('Cannot process a JSON view: invalid view broker loaded.');
	} // end getViewBroker();

	/**
	 * Sets the JSON answer data.
	 *
	 * @param array $answer The answer data.
	 */
	public function setAnswer(array $answer)
	{
		$this->_answer = $answer;
	} // end setAnswer();

	/**
	 * Not needed in this particular case.
	 *
	 * @param Request $request
	 */
	public function setRequest(Request $request)
	{
		/* null */
	} // end setRequest();

	/**
	 * Configures the response object for the JSON transfer.
	 *
	 * @param Response $response The response object.
	 */
	public function setResponse(Response $response)
	{
		$this->_response = $response;
		$response->setHeader('Content-type', 'application/json;charset=utf-8');
	} // end setResponse();

	/**
	 * Converts the answer to JSON and outputs it.
	 */
	public function display()
	{
		$this->_response->setBody(json_encode($this->_answer));
	} // end display();
} // end Json;