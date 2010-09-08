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
namespace Trinity\Web\Request;
use \Trinity\Web\Request;

/**
 * A typical HTTP request.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
class Http extends Request
{
	/**
	 * The visit information
	 * @var \Opc_Visit
	 */
	private $_visit;

	/**
	 * Constructs a HTTP request, importing the information from the visit
	 * data collector provided by Open Power Classes.
	 * 
	 * @param \Opc_Visit $visit The visit information.
	 */
	public function __construct(\Opc_Visit $visit)
	{
		$this->_visit = $visit;
	} // end __construct();

	/**
	 * A wrapper for visit information.
	 * 
	 * @param string $name The key to read
	 */
	public function __get($name)
	{
		return $this->_visit->get($name);
	} // end getRequestMethod();

	/**
	 * Sets the used visit object.
	 * 
	 * @param \Opc_Visit $visit New visit object.
	 */
	public function setVisit(\Opc_Visit $visit)
	{
		$this->_visit = $visit;
	} // end setVisit();

	/**
	 * Returns the currently used visit object.
	 * 
	 * @return \Opc_Visit
	 */
	public function getVisit()
	{
		return $this->_visit;
	} // end getVisit();
} // end Http;