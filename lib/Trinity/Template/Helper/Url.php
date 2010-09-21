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
namespace Trinity\Template\Helper;
use \Trinity\Web\Area\Strategy;
use \Trinity\Web\Router;

class Url
{
	/**
	 * The area discovery strategy.
	 * @var \Trinity\Web\Area\Strategy
	 */
	private $_strategy;

	/**
	 * The router.
	 * @var \Trinity\Web\Router
	 */
	private $_router;

	/**
	 * Default base path, if all other methods fail
	 * @var string
	 */
	private $_base;

	/**
	 * The discoveried base URL-s.
	 * @var array
	 */
	private $_discoveried = array();

	/**
	 * Sets the area discovery strategy, so that we are able to communicate
	 * with it and retrieve area information.
	 * 
	 * @param Strategy $areaStrategy The area strategy.
	 */
	public function setStrategy(Strategy $areaStrategy)
	{
		$this->_strategy = $areaStrategy;
	} // end setStrategy();

	/**
	 * Sets the router.
	 *
	 * @param Router $router The application router.
	 */
	public function setRouter(Router $router)
	{
		$this->_router = $router;
	} // end setStrategy();

	/**
	 * Sets the base URL used where all other methods fail.
	 *
	 * @param string $baseUrl Base URL
	 */
	public function setBaseUrl($baseUrl)
	{
		$this->_base = (string)$baseUrl;
	} // end setBaseUrl();

	/**
	 * Returns the base URL of the specified area. If the area is
	 * not specified, it returns the base URL of the current area.
	 * 
	 * @param string $area Area name.
	 * @return string
	 */
	public function baseUrl($area = null)
	{
		// Default area
		if($area === null)
		{
			list($name, $data) = $this->_strategy->discoverArea();
			if(!isset($data['baseUrl']))
			{
				return $this->_base;
			}
			return $data['baseUrl'];
		}
		// The area has been previously checked.
		elseif(isset($this->_discoveried[$name]))
		{
			return $this->_discoveried[$name];
		}
		// New area
		else
		{
			$area = $this->_strategy->getAreaOptions($name);
			if(!isset($data['baseUrl']))
			{
				$this->_discoveried[$name] = $this->_base;
			}
			else
			{
				$this->_discoveried[$name] = $data['baseUrl'];
			}
			return $this->_discoveried[$name];
		}
	} // end baseUrl();

	/**
	 * Generates an URL for the given arguments.
	 *
	 * @param array $args Router arguments.
	 * @param string $area Area name.
	 * @return string
	 */
	public function assemble($args, $area = null)
	{
		$baseUrl = $this->baseUrl($area);
		$addr = $this->_router->assemble($args);
		if($baseUrl[strlen($baseUrl) -1] == '/' && $addr[0] == '/')
		{
			return $baseUrl.ltrim($addr, '/');
		}
		return $baseUrl.$addr;
	} // end assemble();
} // end Url;