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
use \Trinity\Web\Area\Strategy_Interface;
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
	 * List of kept routed variables.
	 * @var array
	 */
	private $_keptRoutedVars = null;

	/**
	 * The area strategy used for building inter-area routes.
	 * @var \Trinity\Web\Area\Strategy_Interface
	 */
	private $_areaStrategy;

	/**
	 * The query path.
	 * @var string
	 */
	private $_queryPath;

	/**
	 * The base URL.
	 * @var string
	 */
	private $_baseUrl;

	/**
	 * Creates the router.
	 *
	 * @param \Trinity\Web\Area\Strategy_Interface $areaStrategy Area strategy
	 * @param string $queryPath The query path
	 */
	public function __construct($areaStrategy, $queryPath, $baseUrl)
	{
		$this->_areaStrategy = $areaStrategy;
		$this->_queryPath = $queryPath;
		$this->_baseUrl = $baseUrl;
	} // end __construct();

	/**
	 * Sets the list of kept routed variables. These variable values are used
	 * during the URL assembling process, if they have not been defined by
	 * the programmer.
	 *
	 * Implements fluent interface.
	 *
	 * @param array $list The list of kept routed variable names.
	 * @return \Trinity\Web\Router\Standard
	 */
	public function keepRoutedVariables(array $list)
	{
		if($this->_keptRoutedVars === null)
		{
			$this->_keptRoutedVars = array();
		}
		foreach($list as $name)
		{
			$this->_keptRoutedVars[$name] = null;
		}

		return $this;
	} // end keepRoutedVariables();

	/**
	 * Returns the list of kept routed variables.
	 *
	 * @return array
	 */
	public function getKeptRoutedVariables()
	{
		return $this->_keptRoutedVars;
	} // end getKeptRoutedVariables();

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

		// Store kept routed variables.
		if($this->_keptRoutedVars !== null)
		{
			foreach($this->_keptRoutedVars as $name => &$value)
			{
				if(isset($params[$name]))
				{
					$value = $params[$name];
				}
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
		if($this->_keptRoutedVars !== null)
		{
			$sVars = array_merge($this->_keptRoutedVars, $this->_params, $sVars);
		}
		else
		{
			$sVars = array_merge($this->_params, $sVars);
		}
		

		$address = '/';
		if($fullyQualified)
		{
			$baseUrl = $this->_baseUrl.trim($this->_queryPath, '/');
		}
		else
		{
			$baseUrl = rtrim($this->_queryPath, '/');
		}
		

		// Route to other area
		if($area !== null)
		{
			$opts = $this->_areaStrategy->getAreaOptions($area);

			if(isset($opts['baseUrl']))
			{
				if($opts['baseUrl'][strlen($opts['baseUrl']) - 1] == '/')
				{
					$address = '';
					$baseUrl = $opts['baseUrl'];
				}
			}
			else
			{
				$sVars['area'] = $area;
			}
		}

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

		if($address == '/')
		{
			return rtrim($baseUrl.'?'.http_build_query($vars, ''), '?');
		}

		if(sizeof($vars) > 0)
		{
			return rtrim($baseUrl.$address.'?'.http_build_query($vars, ''), '/?');
		}
		return rtrim($baseUrl.$address, '/');
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
} // end Standard;