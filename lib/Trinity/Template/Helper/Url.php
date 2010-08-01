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
namespace Trinity\Template;
use \Trinity\Web\Area\Strategy_Interface;
use \Trinity\Web\Router;

class Helper_Url
{
	/**
	 * The area discovery strategy.
	 * @var \Trinity\Web\Area\Strategy_Interface
	 */
	static private $_strategy;

	/**
	 * The router.
	 * @var \Trinity\Web\Router
	 */
	static private $_router;

	/**
	 * Default base path, if all other methods fail
	 * @var string
	 */
	static private $_base;

	/**
	 * The discoveried base URL-s.
	 * @var array
	 */
	static private $_discoveried = array();

	/**
	 * Sets the area discovery strategy, so that we are able to communicate
	 * with it and retrieve area information.
	 * 
	 * @param Strategy_Interface $areaStrategy The area strategy.
	 */
	static public function setStrategy(Strategy_Interface $areaStrategy)
	{
		self::$_strategy = $areaStrategy;
	} // end setStrategy();

	/**
	 * Sets the router.
	 *
	 * @param Router $router The application router.
	 */
	static public function setRouter(Router $router)
	{
		self::$_router = $router;
	} // end setStrategy();

	/**
	 * Sets the base URL used where all other methods fail.
	 *
	 * @param string $baseUrl Base URL
	 */
	static public function setBaseUrl($baseUrl)
	{
		self::$_base = (string)$baseUrl;
	} // end setBaseUrl();

	/**
	 * Returns the base URL of the specified area. If the area is
	 * not specified, it returns the base URL of the current area.
	 * 
	 * @param string $area Area name.
	 * @return string
	 */
	static public function baseUrl($area = null)
	{
		// Default area
		if($area === null)
		{
			list($name, $data) = self::$_strategy->discoverArea();
			if(!isset($data['baseUrl']))
			{
				return self::$_base;
			}
			return $data['baseUrl'];
		}
		// The area has been previously checked.
		elseif(isset(self::$_discoveried[$name]))
		{
			return self::$_discoveried[$name];
		}
		// New area
		else
		{
			$area = self::$_strategy->getAreaOptions($name);
			if(!isset($data['baseUrl']))
			{
				self::$_discoveried[$name] = self::$_base;
			}
			else
			{
				self::$_discoveried[$name] = $data['baseUrl'];
			}
			return self::$_discoveried[$name];
		}
	} // end baseUrl();

	/**
	 * Generates an URL for the given arguments.
	 *
	 * @param array $args Router arguments.
	 * @param string $area Area name.
	 * @return string
	 */
	static public function url($args, $area = null)
	{
		$baseUrl = self::baseUrl($area);
		$addr = self::$_router->assemble($args);
		if($baseUrl[strlen($baseUrl) -1] == '/' && $addr[0] == '/')
		{
			return $baseUrl.ltrim($addr, '/');
		}
		return $baseUrl.$addr;
	} // end url();
} // end Helper_Url;