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
namespace Trinity\Navigation;
use \Trinity\Navigation\Page\Exception as Page_Exception;

/**
 * The class represents a single page, an element of the navigation
 * tree used by the navigation manager. A collection of pages forms
 * a hierarchical tree.
 *
 * Small note: this class uses some pieces of implementation from
 * Open Power Template 2.1 XML tree classes which use basically the
 * same technique to form trees.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
class Page implements \Iterator
{
	/**
	 * The previous page at the same level.
	 * @var \Trinity\Navigation\Page
	 */
	protected $_prev = null;
	/**
	 * The next page at the same level.
	 * @var \Trinity\Navigation\Page
	 */
	protected $_next = null;
	/**
	 * The parent page.
	 * @var \Trinity\Navigation\Page
	 */
	protected $_parent = null;
	/**
	 * The first child of this page.
	 * @var \Trinity\Navigation\Page
	 */
	protected $_firstChild = null;
	/**
	 * The last child of this page.
	 * @var \Trinity\Navigation\Page
	 */
	protected $_lastChild = null;
	/**
	 * The collection iterator.
	 * @var \Trinity\Navigation\Page
	 */
	protected $_iterator = null;
	/**
	 * The collection iterator position.
	 * @var integer
	 */
	protected $_position = 0;
	/**
	 * The number of children.
	 * @var integer
	 */
	protected $_size = 0;

	/**
	 * Appends a new child to the end of the children list.
	 *
	 * @param Page $child The child to be appended.
	 */
	public function appendChild(Page $child)
	{
		$child->unmount();
		if($this->_lastChild === null)
		{
			$this->_firstChild = $this->_lastChild = $child;
			$child->_parent = $this;
		}
		else
		{
			$child->_previous = $this->_lastChild;
			$child->_parent = $this;
			$this->_lastChild->_next = $child;
			$this->_lastChild = $child;
		}
		$this->_size++;
	} // end appendChild();

	/**
	 * Inserts the new node after the node identified by the '$refnode'. The
	 * reference node can be identified either by the number or by the object.
	 * If the reference node is empty, the new node is appended to the children
	 * list, if the last argument allows for that.
	 *
	 * Note that in case of objective reference node specification, the reference
	 * node must be a child of the current node. Otherwise, an exception is thrown.
	 *
	 * @throws \Trinity\Navigation\Page\Exception
	 * @param Page $newnode The new node.
	 * @param integer|Opt_Xml_Node $refnode The reference node.
	 * @param boolean $appendOnError Do we like to append the node, if $refnode does not exist?
	 */
	public function insertBefore(Page $newnode, $refnode = null, $appendOnError = false)
	{
		if($refnode === null)
		{
			return $this->appendChild($newnode);
		}

		// If the reference node is specified with an integer, we must find it.
		if(is_integer($refnode))
		{
			$i = 0;
			$scan = $this->_firstChild;
			while($scan !== null)
			{
				if($i == $refnode)
				{
					$refnode = $scan;
					break;
				}
				$scan = $scan->_next;
				$i++;
			}
			if(!is_object($refnode))
			{
				if($appendOnError)
				{
					return $this->appendChild($node);
				}
				throw new Page_Exception('The reference node #'.$refnode.' has not been found.');
			}
		}

		// Now, do the insert.
		if($refnode->_parent !== $this)
		{
			if($appendOnError)
			{
				return $this->appendChild($node);
			}
			throw new Page_Exception('The referenced node '.(string)$refnode.' is not a child of the current node '.(string)$this);
		}
		$newnode->unmount();
		if($refnode->_previous !== null)
		{
			$refnode->_previous->_next = $newnode;
			$newnode->_previous = $refnode->_previous;
		}
		$newnode->_next = $refnode;
		$newnode->_parent = $this;
		$refnode->_previous = $newnode;
		if($refnode === $this->_firstChild)
		{
			$this->_firstChild = $newnode;
		}
		$this->_size++;
	} // end insertBefore();

	/**
	 * Removes the child identified either by the number or the object.
	 *
	 * @param integer|\Trinity\Navigation\Page $node The node to be removed.
	 * @return boolean
	 */
	public function removeChild($node)
	{
		if(is_integer($node))
		{
			$i = 0;
			$scan = $this->_firstChild;
			while($scan !== null)
			{
				if($i == $node)
				{
					$node = $scan;
					break;
				}
				$scan = $scan->_next;
				$i++;
			}
			if(!is_object($node))
			{
				return false;
			}
		}

		// Check if this is really our child. We cannot exterminate the
		// children of other nodes.
		if($node->_parent !== $this)
		{
			return false;
		}
		$this->_size--;

		// Iteration...
		if($this->_iterator === $node)
		{
			$this->_iterator = $node->_previous;
			if($this->_iterator === null)
			{
				$this->_iterator = -1;
			}
		}

		// The border cases...
		if($this->_firstChild === $node)
		{
			$this->_firstChild = $node->_next;
		}
		if($this->_lastChild === $node)
		{
			$this->_lastChild = $node->_previous;
		}

		// Unlink it.
		if($node->_previous !== null)
		{
			$node->_previous->_next = $node->_next;
		}
		if($node->_next !== null)
		{
			$node->_next->_previous = $node->_previous;
		}
		$node->_parent = null;
		$node->_previous = null;
		$node->_next = null;
		return true;
	} // end removeChild();

	/**
	 * An implementation of the method from the Iterator interface. It
	 * sets the internal collection pointer to the first element of the
	 * collection.
	 */
	public function rewind()
	{
		$this->_iterator = $this->_first;
		$this->_position = 0;
	} // end rewind();

	/**
	 * An implementation of the method from the Iterator interface. It tests
	 * whether the current collection pointer is valid and returns it as
	 * a 'true' or 'false' value.
	 *
	 * @return boolean
	 */
	public function valid()
	{
		return ($this->_iterator !== null);
	} // end valid();

	/**
	 * An implementation of the method from the Iterator interface. Returns
	 * the element currently visited by the collection pointer. If the pointer
	 * is invalid, the method returns 'null'.
	 *
	 * @return Opt_Xml_Scannable
	 */
	public function current()
	{
		return $this->_iterator;
	} // end current();

	/**
	 * An implementation of the method from the Iterator interface. Moves the
	 * collection pointer to the next element. Please note this method assumes
	 * that the current pointer is valid.
	 *
	 * @throws OutOfBoundsException
	 */
	public function next()
	{
		if($this->_iterator === null)
		{
			throw new \OutOfBoundsException('\Trinity\Navigation\Page has already reached the end of a collection.');
		}
		if($this->_iterator === -1)
		{
			$this->_iterator = $this->_first;
			$this->_position = 0;
		}
		else
		{
			$this->_iterator = $this->_iterator->_next;
			$this->_position++;
		}
	} // end next();

	/**
	 * An implementation of the method from the Iterator interface. Returns
	 * the key of the current pointer position in a collection.
	 *
	 * @return integer
	 */
	public function key()
	{
		return $this->_position;
	} // end key();
} // end Page;
