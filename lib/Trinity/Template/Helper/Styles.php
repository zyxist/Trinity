<?php
/*
 * TRINITY FRAMEWORK <http://www.invenzzia.org>
 *
 * This file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE. It is also available through
 * WWW at this URL: <http://www.invenzzia.org/license/new-bsd>
 *
 * Copyright (c) Invenzzia Group <http://www.invenzzia.org>
 * and other contributors. See website for details.
 */
namespace Trinity\Template\Helper;
use Trinity\Template\Helper\Styles\CSSCompressor;

class Styles
{
	private
		$_minify = true,
		$_base = null,
		$_options = array(
			'cache_directory' => 'cache/',
		),
		$_styles = array(),
		$_localStyles = array(),
		$_compressor = null;

	/**
	 * Sets the base URL for local files.
	 *
	 * @param string $baseUrl Base URL
	 */
	public function setBaseUrl($baseUrl)
	{
		$this->_base = (string)$baseUrl;
	} // end setBaseUrl();

	/**
	 * Returns the base URL for local files.
	 *
	 * @return string
	 */
	public function getBaseUrl()
	{
		return $this->_base;
	} // end getBaseUrl();

	/**
	 * Sets minify css files feature state.
	 *
	 * @param string $baseUrl Base URL
	 */
	public function setMinify($state)
	{
		$this->_minify = (bool)$state;
		if(!(bool)$state)
		{
			for($i = 0, $endI = count($this->_styles); $i<$endI; $i++)
			{
				if(in_array($this->_styles[$i], $this->_localStyles))
				{
					$this->_styles[$i] = $this->getBaseUrl().$this->_styles[$i];
				}
			}
		}
	} // end setMinify();

	/**
	 * Returns if we want to minify css files.
	 *
	 * @return string
	 */
	public function getMinify()
	{
		return $this->_minify;
	} // end getMinify();

	/**
	 * Sets where helper should save minified files.
	 *
	 * @param string $dir Directory.
	 */
	public function setCacheDirectory($dir)
	{
		if($dir[strlen($dir)-1] != '/')
		{
			$dir .= '/';
		}
		$this->_options['cache_directory'] = (string)$dir;
	} // end setCacheDirectory();

	/**
	 * Returns the cache directory.
	 *
	 * @return string
	 */
	public function getCacheDirectory()
	{
		return $this->_options['cache_directory'];
	} // end getCacheDirectory();

	/**
	 * Sets an option for CSSCompressor.
	 *
	 * @param string $name Option name
	 * @param mixed $value Value
	 */
	public function set($name, $value)
	{
		$this->_options[$name] = $value;
	} // end set();

	/**
	 * Returns option value if set, otherwise null.
	 * 
	 * @param string $name Option name.
	 * @return mixed
	 */
	public function get($name)
	{
		return isset($this->_options[$name])?$this->_options[$name]:null;
	} // end get();

	/**
	 * Appends style.
	 *
	 * @param String $style Path to style with its name.
	 * @param Boolean $local Optional Is it on local server?
	 */
	public function append($style, $local = true)
	{
		if((bool)$local)
		{
			$this->_localStyles[] = $style;
		}
		$this->_styles[] = $style;
	} // end append();

	/**
	 * Prepends style.
	 *
	 * @param String $style Path to style with its name.
	 * @param Boolean $local Is it on local server?
	 */
	public function prepend($style, $local = true)
	{
		if((bool)$local)
		{
			$this->_localStyles[] = $style;
		}
		\array_unshift($this->_styles, $style);
	} // end prepend();

	/**
	 * Returns array of addresses to styles. Also minifies them and compresses
	 * (on demand - configurable).
	 * 
	 * @return array
	 */
	public function getStyles()
	{
		if($this->getMinify())
		{
			if($this->_compressor === null)
			{
				$this->_compressor = new CSSCompressor($this->_options);
			}
			$this->_compressor->addFiles($this->_styles);
			return array($this->getBaseUrl().$this->_compressor->minifyCode());
		}
		else
		{
			for($i = 0, $endI = count($this->_styles); $i<$endI; $i++)
			{
				if(in_array($this->_styles[$i], $this->_localStyles))
				{
					$this->_styles[$i] = $this->getBaseUrl().$this->_styles[$i];
				}
			}
		}
		return $this->_styles;
	} // end getStyles();
} // end Styles;