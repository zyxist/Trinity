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
 * The standard router implementation.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
class Router_Standard implements Router_Interface
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
	 * Should we generate an URL with the fully qualified path?
	 * @var boolean
	 */
	private $_fqPath;

	/**
	 * Sets the fully qualified path status, i.e. for generating URL-s
	 * for redirection.
	 *
	 * @param boolean $fqPath The new status
	 */
	public function setFullyQualifiedPath($fqPath)
	{
		$this->_fqPath = (boolean)$fqPath;
	} // end setFullyQualifiedPath();

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
		return $params;
	} // end createParams();

	/**
	 * Returns an URL for the specified parameter list.
	 *
	 * @param Router_Exception
	 * @param array $sVars An array of parameters
	 * @return string
	 */
	public function assemble(array $sVars)
	{
		if(!is_array($sVars))
		{
			throw new Router_Exception('The variable list passed to router is not an array.');
		}
		$address = '/';
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
		if($address[0] == '/')
		{
			$path = rtrim($this->_path, '/');
		}
		else
		{
			$path = $this->_path;
		}

		if($address == '/')
		{
			return $path.'?'.http_build_query($vars, '');
		}

		if(sizeof($vars) > 0)
		{
			return $path.$address.'?'.http_build_query($vars, '');
		}
		return $path.$address;
	} // end assemble();
} // end Router_Standard;