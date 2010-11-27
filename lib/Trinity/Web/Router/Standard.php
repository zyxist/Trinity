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

namespace Trinity\Web\Router;
use \Trinity\Web\Area\Manager;
use \Trinity\Web\Router as Router_Interface;
use \Trinity\Web\Router\Exception as Router_Exception;

/**
 * The standard router implementation.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
class Standard implements Router_Interface
{
	const COMPULSORY = 1;
	const OPTIONAL = 0;

	/**
	 * The list of routing patterns.
	 * @var array
	 */
	private $patterns = array();

	/**
	 * The configuration link
	 * @var string
	 */
	private $_path = null;

	/**
	 * The router predefined parameters.
	 * @var array
	 */
	private $_params = array();

	/**
	 * The area manager.
	 * @var \Trinity\Web\Area\Manager
	 */
	protected $_manager;

	/**
	 * The resolved base URL-s.
	 * @var array
	 */
	protected $_baseUrls = array();

	/**
	 * The resolved query paths.
	 * @var array
	 */
	protected $_queryPaths = array();

	/**
	 * Creates the router.
	 *
	 * @param \Trinity\Web\Area\Manager $manager The area manager.
	 */
	public function __construct(Manager $manager)
	{
		$this->_manager = $manager;
	} // end __construct();

	/**
	 * Adds a routing pattern to the router.
	 *
	 * @param string $pattern The address pattern.
	 * @param array $options The list of variables used in the address pattern.
	 * @param array $requirements Additional list of regular expressions to validate the variable.
	 */
	public function connect($pattern, $options, $requirements = array())
	{
		preg_match_all('/(([^\/]){1}(\/\/)?){1,}/',$pattern, $found);

		$regexArr = $regexIds = $varRegex = $required = $optional = $arrayVals = array();
		foreach($found[0] as $item)
		{
			$isVar = $item[0] == ':';
			$item = ltrim($item, ':');
			if($isVar && !isset($options[$item]))
			{
				$options[$item] = self::OPTIONAL;
			}

			if(!$isVar)
			{
				$regexArr[] = '('.$item.'){1}';
				$regexIds[] = NULL;
			}
			else
			{
				if(!isset($requirements[$item]))
				{
					$requirements[$item] = '[^\/]+';
				}
				$varRegex[$item] = $requirements[$item];
				if($options[$item] === self::COMPULSORY)
				{
					$regexArr[] = '('.$requirements[$item].'){1}';
					$required[] = $item;
				}
				elseif($options[$item] === self::OPTIONAL)
				{
					$regexArr[] = '('.$requirements[$item].')?';
					$optional[] = $item;
				}
				else
				{
					$regexArr[] = '('.$item.'){1}';
					$arrayVals[$item] = $options[$item];
				}
				unset($options[$item]);
				$regexIds[] = $item;
			}
		}

		$arrayVals = array_merge($options, $arrayVals);

		$this->patterns[] = array(
			'regex' => '/^\\/'.implode('\\/', $regexArr).'[\\/]*/',
			'regexIds' => $regexIds,
			'singleRegex' => $regexArr,
			'varRegex' => $varRegex,
			'required' => $required,
			'optional' => $optional,
			'arrayVals' => $arrayVals,
			'build' => $found[0]
		);
	} // end connect();

	/**
	 * Parses the HTTP request arguments and returns the list of URL
	 * parameters.
	 *
	 * @param string $path The path to analyze
	 * @return array
	 */
	public function route($path)
	{
		$params = array();
		foreach($this->patterns as $pattern)
		{
			if(preg_match($pattern['regex'], $path, $found))
			{
				$vars = array();
				foreach($found as $id => $item)
				{
					if($id < 1)
					{
						continue;
					}
					$id--;

					if(preg_match('/'.$pattern['singleRegex'][$id].'/', $item))
					{
						if(!is_null($pattern['regexIds'][$id]))
						{
							$vars[$pattern['regexIds'][$id]] = $item;
						}
					}
				}

				foreach($pattern['required'] as $required)
				{
					if(!isset($vars[$required]))
					{
						continue 2;
					}
				}

				foreach($pattern['optional'] as $optional)
				{
					if(!isset($vars[$optional]))
					{
						$vars[$optional] = NULL;
					}
				}

				$params = array_merge($vars, $pattern['arrayVals']);
				break;
			}
		}

		// TODO: FIX!
		foreach($_GET as $name => $value)
		{
			if(!isset($params[$name]))
			{
				$params[$name] = $value;
			}
		}
		return $params;
	} // end createParams();

	/**
	 * Returns an URL for the specified parameter list. If the area name
	 * is not specified, we assume that the route links to the current
	 * area.
	 *
	 * @throws \Trinity\Web\Router\Exception
	 * @param array $sVars An array of parameters
	 * @param string $area The area name.
	 * @param boolean $fullyQualified Use fully qualified paths?
	 * @return string
	 */
	public function assemble(array $sVars, $area = null, $fullyQualified = false)
	{
		if(!is_array($sVars))
		{
			throw new Router_Exception('The variable list passed to router is not an array.');
		}
		$sVars = array_merge($this->_params, $sVars);
		foreach($sVars as $name => &$var)
		{
			if($var === null)
			{
				unset($sVars[$name]);
			}
		}

		$address = $this->queryPath($area);
		if($address[strlen($address) - 1] != '/')
		{
			$address .= '/';
		}
		$initialAddress = $address;

		// Build the argument list
		foreach($this->patterns as $pattern)
		{
			$vars = $sVars;
			foreach($pattern['required'] as $required)
			{
				if(!isset($vars[$required]))
				{
					continue 2;
				}
				if(!preg_match('/'.$pattern['varRegex'][$required].'/', $vars[$required]))
				{
					continue 2;
				}
			}

			foreach($pattern['arrayVals'] as $id => $val)
			{
				if(!isset($vars[$id]))
				{
					continue 2;
				}
				if($vars[$id] != $val)
				{
					continue 2;
				}
				unset($vars[$id]);
			}
			foreach($pattern['optional'] as $optional)
			{
				if(!empty($vars[$optional]))
				{
					if(!preg_match('/'.$pattern['varRegex'][$optional].'/', $vars[$optional]))
					{
						continue 2;
					}
				}
			}
			foreach($pattern['build'] as $item)
			{
				$isVar = $item[0] == ':';
				$item = ltrim($item, ':');
				if(!$isVar)
				{
					$address .= $item.'/';
				}
				else
				{
					if(in_array($item, $pattern['required']) || !empty($vars[$item]))
					{
						$address .= $vars[$item].'/';
						unset($vars[$item]);
					}
					else
					{
						$address .= '/';
					}
				}
			}
			break;
		}

		if($address == $initialAddress)
		{
			return $address.'?'.http_build_query($vars, '');
		}

		if(sizeof($vars) > 0)
		{
			return $address.'?'.http_build_query($vars, '');
		}
		return $address;
	} // end assemble();

	/**
	 * Sets the default router parameter.
	 *
	 * @param string $name
	 * @param string $value
	 */
	public function setParam($name, $value)
	{
		$this->_params[(string)$name] = $value;
	} // end setParam();

	/**
	 * Sets the predefined router variables.
	 *
	 * @param mixed $variables The list of router variables.
	 */
	public function setParams(array $variables)
	{
		foreach($variables as $name => $value)
		{
			$this->_params[(string)$name] = $value;
		}
	} // end setParams();

	/**
	 * Returns the base URL of the specified area. If the area is
	 * not specified, it returns the base URL of the current area.
	 *
	 * @param string $area Area name.
	 * @return string
	 */
	public function baseUrl($area = null)
	{
		// Active area
		if($area === null)
		{
			if(!isset($this->_baseUrls['__DEFAULT__']))
			{
				$this->_baseUrls['__DEFAULT__'] = $this->_manager->getActiveArea()->get('baseUrl');
			}
			return $this->_baseUrls['__DEFAULT__'];
		}

		// Other areas
		if(!isset($this->_baseUrls[$area]))
		{
			$metadata = $this->_manager->getAreaMetadata($area);
			if(!isset($metadata['baseUrl']))
			{
				$this->_baseUrls[$area] = $this->baseUrl(null);
			}
			else
			{
				$this->_baseUrls[$area] = $metadata['baseUrl'];
			}
		}
		return $this->_baseUrls[$area];
	} // end baseUrl();

	/**
	 * Returns the query path for the given area.
	 *
	 * @param string $area Area name
	 * @return string
	 */
	public function queryPath($area = null)
	{
		// Active area
		if($area === null)
		{
			if(!isset($this->_queryPaths['__DEFAULT__']))
			{
				$this->_queryPaths['__DEFAULT__'] = $this->_manager->getActiveArea()->get('queryPath');
			}
			return $this->_queryPaths['__DEFAULT__'];
		}

		// Other areas
		if(!isset($this->_queryPaths[$area]))
		{
			$metadata = $this->_manager->getAreaMetadata($area);
			if(!isset($metadata['queryPath']))
			{
				$this->_queryPaths[$area] = $this->baseUrl(null);
			}
			else
			{
				$this->_queryPaths[$area] = $metadata['queryPath'];
			}
		}
		return $this->_queryPaths[$area];
	} // end queryPath();
} // end Standard;