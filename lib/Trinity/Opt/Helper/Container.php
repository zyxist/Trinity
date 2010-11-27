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
namespace Trinity\Opt\Helper;
use \IteratorAggregate;
use \Countable;
use \ArrayObject;

/**
 * A container for various elements
 *
 * @copyright Copyright (c) Invenzzia Group 2009
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
class Container implements IteratorAggregate, Countable
{
	/**
	 * The list of elements.
	 *
	 * @var Array
	 */
	protected $_elements = array();

	/**
	 * An extra array for detecting duplicates.
	 *
	 * @var Array
	 */
	private $_mappedElements = array();
	/**
	 * Do we accept arbitrary attributes?
	 *
	 * @var Boolean
	 */
	private $_allowArbitraryAttributes = false;

	/**
	 * Accepted attributes
	 *
	 * @var Array
	 */
	protected $_acceptedAttributes = array();

	/**
	 * Sets the state of arbitrary attributes which are disabled
	 * by default.
	 *
	 * @param Boolean $state The new state
	 */
	public function setAllowArbitraryAttributes($state)
	{
		$this->_allowArbitraryAttributes = (bool)$state;
	} // end setAllowArbitraryAttributes();

	/**
	 * Returns an iterator for the element list.
	 *
	 * @return ArrayObject
	 */
	public function getIterator()
	{
		return new ArrayObject($this->_elements);
	} // end getIterator();

	/**
	 * Returns the number of elements in the list.
	 *
	 * @return Integer
	 */
	public function count()
	{
		return sizeof($this->_elements);
	} // end count();

	/**
	 * Prepends a new element to the list.
	 *
	 * @param String $id An identifier used to detect duplicates.
	 * @param Array $options Options
	 */
	protected function _prepend($id, $options)
	{
		if($id !== null && ($location = \array_search($id, $this->_mappedElements)) !== false)
		{
			$this->_offsetSet($location, $id, $options);
		}
		else
		{
			\array_unshift($this->_elements, $options);
			\array_unshift($this->_mappedElements, $id);
		}
	} // end _prepend();

	/**
	 * Appends a new element to the list.
	 *
	 * @param String $id An identifier used to detect duplicates.
	 * @param Array $options Options
	 */
	protected function _append($id, $options)
	{
		if($id !== null && ($location = \array_search($id, $this->_mappedElements)) !== false)
		{
			$this->_offsetSet($location, $id, $options);
		}
		else
		{
			\array_push($this->_elements, $options);
			\array_push($this->_mappedElements, $id);
		}
	} // end _append();

	/**
	 * Sets a new element in the specified location of the list.
	 *
	 * @param Integer $offset The offset.
	 * @param String $id An identifier used to detect duplicates.
	 * @param Array $options Options
	 */
	protected function _offsetSet($offset, $id, $options)
	{
		if($id !== null && ($location = \array_search($id, $this->_mappedElements)) !== false)
		{
			$offset = $location;
		}

		$this->_elements[$offset] = $options;
		$this->_mappedElements[$offset] = $id;
	} // end _offsetSet();

	/**
	 * Filters attributes according to the arbitrary attributes state
	 * and the accepted attribute list.
	 *
	 * @param Array $options The list of input attributes.
	 * @return Array
	 */
	protected function _filterAttributes($options)
	{
		$output = array();
		if($this->_allowArbitraryAttributes)
		{
			// TODO: Provide a better and still quite fast attribute name control.
			foreach($options as $name => $value)
			{
				if(!ctype_digit($name))
				{
					$output[$name] = $value;
				}
			}
		}
		else
		{
			foreach($this->_acceptedAttributes as $name)
			{
				if(isset($options[$name]))
				{
					$output[$name] = $options[$name];
				}
			}
		}
		return $output;
	} // end _filterAttributes();

	/**
	 * Returns the data for OPT sections. Called by the data format.
	 * @return Array
	 */
	public function toArray()
	{
		return $this->_elements;
	} // end toArray();
} // end Container;