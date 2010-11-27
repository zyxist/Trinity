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
namespace Trinity\Web\Area\MetadataLoader;
use \Trinity\Web\Area\MetadataLoader;

/**
 * The abstract class for the loaders that read the metadata from the
 * filesystem.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
abstract class FileLoader implements MetadataLoader
{
	/**
	 * The current file.
	 * @var string
	 */
	protected $_currentFile;

	/**
	 * The list of scanned paths.
	 * @var array
	 */
	protected $_paths;

	/**
	 * The loaded metadata.
	 * @var array
	 */
	protected $_metadata;

	/**
	 * Is the data loaded?
	 * @var boolean
	 */
	protected $_loaded = false;

	/**
	 * Creates the file loader.
	 * 
	 * @param array|string $paths The list of paths, where to look for the files.
	 */
	public function __construct($paths = array())
	{
		if(!is_array($paths))
		{
			$paths = array($paths);
		}
		foreach($paths as &$path)
		{
			if($path[strlen($path) - 1] != DIRECTORY_SEPARATOR)
			{
				$path .= DIRECTORY_SEPARATOR;
			}
		}
		$this->_paths = $paths;
	} // end __construct();

	/**
	 * Sets the file name, which the metadata will be loaded from.
	 * 
	 * @param string $file The file with the metadata.
	 */
	public function setFile($file)
	{
		$this->_currentFile = $file;
	} // end setFile();

	/**
	 * Returns the file name, which the metadata will be loaded from.
	 * 
	 * @return string
	 */
	public function getFile()
	{
		return $this->_currentFile;
	} // end getFile();

	/**
	 * Returns true, if all the area metadata are preloaded, so we can
	 * refresh the cache.
	 * 
	 * @return boolean
	 */
	public function isPreloaded()
	{
		return $this->_loaded;
	} // end isPreloaded();

	/**
	 * Performs the actual lazy-loading.
	 */
	abstract protected function _doLoad();

	/**
	 * Loads the metadata for the given area.
	 *
	 * @param string $areaKey The area key
	 * @return array
	 */
	public function loadMetadata($areaKey)
	{
		if($this->_metadata === null)
		{
			$this->_doLoad();
		}
		if(!isset($this->_metadata[$areaKey]))
		{
			return null;
		}
		return $this->_metadata[$areaKey];
	} // end loadMetadata();

	/**
	 * Returns a validated path to the specified file. If the file name is
	 * invalid or it does not exist in any of defined paths, an exception
	 * is thrown.
	 *
	 * @throws InvalidArgumentException
	 * @param string $filename The file name
	 * @return string
	 */
	public function findFile($filename)
	{
		foreach($this->_paths as $path)
		{
			if(file_exists($path.$filename))
			{
				return $path.$filename;
			}
		}
		throw new \InvalidArgumentException('The file \''.$filename.'\' does not exist in any of the specified paths.');
	} // end findFile();

	/**
	 * Unsupported method.
	 * 
	 * @throws BadMethodCallException
	 * @param string $areaKey Area key
	 */
	public function loadAreaMetadata($areaKey)
	{
		throw new \BadMethodCallException('This loader does not support lazy-loading area metadata.');
	} // end loadAreaMetadata();
} // end FileLoader;