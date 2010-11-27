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

/**
 * The concrete implementation of the area metadata loader that loads them
 * from an INI file.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
class IniLoader extends FileLoader
{
	/**
	 * Loads the area metadata from an INI file.
	 */
	protected function _doLoad()
	{
		if($this->_currentFile === null)
		{
			throw new \DomainException('Cannot load area metadata: the file with the area definitions is not defined.');
		}

		$data = \parse_ini_file($this->findFile($this->_currentFile), true);

		foreach($data as $name => &$item)
		{
			if(!is_array($item))
			{
				throw new \DomainException('Cannot load area metadata: the file must not contain top-level options: \''.$name.'\'');
			}
		}

		$this->_metadata = $data;
	} // end _doLoad();
} // end IniLoader;