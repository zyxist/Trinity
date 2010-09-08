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
 * View brokers contain the concrete code for displaying something, because
 * the process can be separated from the view code.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
interface View_Broker
{
	/**
	 * Sets the request object.
	 *
	 * @param Request $request The request object.
	 */
	public function setRequest(Request $request);

	/**
	 * Sets the response object.
	 *
	 * @param Response $response The response object.
	 */
	public function setResponse(Response $response);

	/**
	 * Displays the view.
	 */
	public function display();
} // end View_Broker;