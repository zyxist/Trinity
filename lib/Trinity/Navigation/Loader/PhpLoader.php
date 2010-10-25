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
namespace Trinity\Navigation\Loader;
use \SimpleXmlElement;
use \SplQueue;

/**
 * The concrete implementation of the navigation structure loader.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
class PhpLoader extends FileLoader
{
	/**
	 * Builds a navigation tree from a PHP file.
	 *
	 * @return \Trinity\Navigation\Page
	 */
	public function buildNavigationTree()
	{
		if($this->_currentFile === null)
		{
			throw new \DomainException('Cannot load navigation structure: the file with the structure is not defined.');
		}

		// This INCLUDE should return the values.
		include $this->findFile($this->_currentFile);

		// No pages returned.
		return NULL;
	} // end buildNavigationTree();
} // end PhpLoader;