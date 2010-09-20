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
namespace Trinity\Template;
use Trinity\Template\Helper\Styles\CSSCompressor;

class Helper_Styles
{
	static private
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
	static public function setBaseUrl($baseUrl)
	{
		self::$_base = (string)$baseUrl;
	} // end setBaseUrl();

	/**
	 * Returns the base URL for local files.
	 *
	 * @return string
	 */
	static public function getBaseUrl()
	{
		return self::$_base;
	} // end getBaseUrl();

	/**
	 * Sets minify css files feature state.
	 *
	 * @param string $baseUrl Base URL
	 */
	static public function setMinify($state)
	{
		self::$_minify = (bool)$state;
		if(!(bool)$state)
		{
			for($i = 0, $endI = count(self::$_styles); $i<$endI; $i++)
			{
				if(in_array(self::$_styles[$i], self::$_localStyles))
				{
					self::$_styles[$i] = self::getBaseUrl().self::$_styles[$i];
				}
			}
		}
	} // end setMinify();

	/**
	 * Returns if we want to minify css files.
	 *
	 * @return string
	 */
	static public function getMinify()
	{
		return self::$_minify;
	} // end getMinify();

	/**
	 * Sets where helper should save minified files.
	 *
	 * @param string $dir Directory.
	 */
	static public function setCacheDirectory($dir)
	{
		if($dir[strlen($dir)-1] != '/')
		{
			$dir .= '/';
		}
		self::$_options['cache_directory'] = (string)$dir;
	} // end setCacheDirectory();

	/**
	 * Returns the cache directory.
	 *
	 * @return string
	 */
	static public function getCacheDirectory()
	{
		return self::$_options['cache_directory'];
	} // end getCacheDirectory();

	/**
	 * Sets an option for CSSCompressor.
	 *
	 * @param string $name Option name
	 * @param mixed $value Value
	 */
	static public function set($name, $value)
	{
		self::$_options[$name] = $value;
	} // end set();

	/**
	 * Returns option value if set, otherwise null.
	 * 
	 * @param string $name Option name.
	 * @return mixed
	 */
	static public function get($name)
	{
		return isset(self::$_options[$name])?self::$_options[$name]:null;
	} // end get();

	/**
	 * Appends style.
	 *
	 * @param String $style Path to style with its name.
	 * @param Boolean $local Optional Is it on local server?
	 */
	static public function append($style, $local = true)
	{
		if((bool)$local)
		{
			self::$_localStyles[] = $style;
		}
		self::$_styles[] = $style;
	} // end append();

	/**
	 * Prepends style.
	 *
	 * @param String $style Path to style with its name.
	 * @param Boolean $local Is it on local server?
	 */
	static public function prepend($style, $local = true)
	{
		if((bool)$local)
		{
			self::$_localStyles[] = $style;
		}
		\array_unshift(self::$_styles, $style);
	} // end prepend();

	/**
	 * Returns array of addresses to styles. Also minifies them and compresses
	 * (on demand - configurable).
	 * 
	 * @return array
	 */
	static public function getStyles()
	{
		if(self::getMinify())
		{
			if(self::$_compressor === null)
			{
				self::$_compressor = new CSSCompressor(self::$_options);
			}
			self::$_compressor->addFiles(self::$_styles);
			return array(self::getBaseUrl().self::$_compressor->minifyCode());
		}
		else
		{
			for($i = 0, $endI = count(self::$_styles); $i<$endI; $i++)
			{
				if(in_array(self::$_styles[$i], self::$_localStyles))
				{
					self::$_styles[$i] = self::getBaseUrl().self::$_styles[$i];
				}
			}
		}
		return self::$_styles;
	} // end getStyles();
} // end Helper_Styles;