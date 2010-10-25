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
namespace Trinity\Navigation\Helper;
use \Countable;
use \Iterator;
use \Trinity\Navigation\Manager;

/**
 * The helper for iterating through the active page path to the root
 * of the navigation tree. Can be used to build titles and breadcrumbs.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
class ActivePath extends Traversal implements Iterator, Countable
{
	const ASCENDING = 0;
	const DESCENDING = 1;

	protected $_iteratorMode = self::ASCENDING;
	protected $_preparedPath;
	protected $_preparedPathIdx;

	public function setIteratorMode($mode)
	{
		$this->_iteratorMode = (integer)$mode;
	} // end setIteratorMode();

	public function getIteratorMode()
	{
		return $this->_iteratorMode;
	} // end getIteratorMode();

	public function count()
	{
		$this->_build();
		return sizeof($this->_preparedPath);
	} // end count();

	public function rewind()
	{
		$this->_build();

		if($this->_iteratorMode == self::ASCENDING)
		{
			$this->_preparedPathIdx = 0;
		}
		else
		{
			$this->_preparedPathIdx = sizeof($this->_preparedPath) - 1;
		}
	} // end rewind();

	public function valid()
	{
		return isset($this->_preparedPath[$this->_preparedPathIdx]);
	} // end valid();

	public function next()
	{
		if($this->_iteratorMode == self::ASCENDING)
		{
			$this->_preparedPathIdx++;
		}
		else
		{
			$this->_preparedPathIdx--;
		}
	} // end next();

	public function current()
	{
		return $this->_preparedPath[$this->_preparedPathIdx];
	} // end current();

	public function key()
	{
		return $this->_preparedPathIdx;
	} // end key();

	protected function _build()
	{
		if(is_array($this->_preparedPath))
		{
			return true;
		}
		$page = $this->_manager->getActivePage();
		if($page === null)
		{
			$this->_preparedPath = array();
			$this->_preparedPathIdx = 0;
		}

		while($page !== null)
		{
			if($this->_canRender($page))
			{
				$this->_preparedPath[] = $page;
			}
			$page = $page->getParent();
		}
	} // end _build();
} // end ActivePath;