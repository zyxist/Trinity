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

namespace Trinity\Web\Broker;
use \Trinity\Web\Request\Http as Request_Http;
use \Trinity\Web\Response\Http as Response_Http;

/**
 * The standard web application broker that constructs requests and responses
 * for HTTP environment integrated with Open Power Template.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
class Standard extends Broker
{

	/**
	 * Builds the request using the visit information.
	 * 
	 * @param \Opc_Visit $visit The visit information object.
	 */
	public function buildRequest(\Opc_Visit $visit)
	{
		$this->setRequest($request = new Request_Http($visit));

		$this->_application->getEventManager()->fire('web.broker.request.create', array(
			'request' => $request
		));
	} // end buildRequest();

	/**
	 * Builds the response.
	 */
	public function buildResponse()
	{
		$this->setResponse($response = new Response_Http());

		$this->_application->getEventManager()->fire('web.broker.response.create', array(
			'response' => $response
		));
	} // end buildResponse();
} // end Broker_Standard;