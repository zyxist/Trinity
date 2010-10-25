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
	const TYPE_REAL = 0;
	const TYPE_URL = 1;
	const TYPE_STRUCTURAL = 2;

	const RENDER_ALWAYS = 0;
	const RENDER_ON_ACTIVE = 1;
	const RENDER_ON_INACTIVE = 2;
	const RENDER_SELECTION_SPECIFIC = 3;
	const RENDER_NEVER = 4;
	const RENDER_IF_PARENT = 5;
	const RENDER_CHILDREN = 6;

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
	 * The page data.
	 * @var array
	 */
	protected $_data = array();
	/**
	 * The navigation tree hook.
	 * @var Hook
	 */
	protected $_hook = null;
	/**
	 * The page type class.
	 * @var integer
	 */
	protected $_pageType = self::TYPE_REAL;
	/**
	 * The rendering behaviour for various helper classes.
	 * @var array
	 */
	protected $_renderClasses = array();
	/**
	 * Have the page data been read from the hook?
	 * @var boolean
	 */
	protected $_hookReadData = false;
	/**
	 * Have the children been read from the hook?
	 * @var boolean
	 */
	protected $_hookReadChildren = false;

	/**
	 * Creates the page object.
	 * 
	 * @param array $data The optional, initial page data.
	 */
	public function __construct($data = array())
	{
		$this->_data = $data;
	} // end __construct();

	/**
	 * Sets the page information hook.
	 * 
	 * @param Hook $hook The page hook.
	 */
	public function setHook(Hook $hook)
	{
		$this->_hook = $hook;
	} // end setHook();

	/**
	 * Returns the page information hook.
	 * @return Hook
	 */
	public function getHook()
	{
		return $this->_hook;
	} // end getHook();

	public function unmount()
	{

	} // end unmount();

	/**
	 * Sets the page type. If the page type is invalid, an exception
	 * is thrown.
	 *
	 * @throws \Trinity\Navigation\Page\Exception
	 * @param integer $type The new page type.
	 */
	public function setPageType($type)
	{
		if($type < 0 || $type > 2)
		{
			throw new Page_Exception('Invalid page type: '.$type);
		}
		$this->_pageType = $type;
	} // end setPageType();

	/**
	 * Returns the page type.
	 * @return integer
	 */
	public function getPageType()
	{
		return $this->_pageType;
	} // end getPageType();

	/**
	 * Sets the rendering properties for the given class. Note that the
	 * rendering classes have nothing to do with OOP classes, but rather
	 * identify various helper types.
	 *
	 * @throws \Trinity\Navigation\Page\Exception
	 * @param string $className the rendering class name.
	 * @param integer $renderProps The new rendering properties.
	 * @return integer
	 */
	public function setRenderClass($className, $renderProps)
	{
		if($renderProps < 0 || $renderProps > 5)
		{
			throw new Page_Exception('Invalid rendering property for class \''.$className.'\': '.$renderProps);
		}
		$this->_renderClasses[$className] = $renderProps;
	} // end setRenderClass();

	/**
	 * Returns the rendering properties for the given class.
	 *
	 * @return integer
	 */
	public function getRenderClass($className, $default = null)
	{
		if(!isset($this->_renderClasses[$className]))
		{
			return $default;
		}
		return $this->_renderClasses[$className];
	} // end getRenderClass();

	/**
	 * Returns the page property. Note that it can optionally lazy-load the
	 * missing properties from the hook, if any is set.
	 *
	 * @param string $name The page property name.
	 * @return mixed
	 */
	public function __get($name)
	{
		if(!isset($this->_data[$name]))
		{
			// Lazy-load the data from the hook.
			if(isset($this->_hook) && !$this->_hookReadData)
			{
				$this->_hook->createPageInfo($this);
				$this->_hookReadData = true;
				// If still does not exist, the name is invalid.
				if(!isset($this->_data[$name]))
				{
					return null;
				}
			}
			else
			{
				return null;
			}
		}
		return $this->_data[$name];
	} // end __get();

	/**
	 * Sets the new value of the page property.
	 * 
	 * @param string $name The property name.
	 * @param mixed $value The property value.
	 */
	public function __set($name, $value)
	{
		$this->_data[$name] = $value;
	} // end __set();

	/**
	 * Checks if the specified page property exists. The method can optionally
	 * lazy-load the extra properties from the hook, if any is defined.
	 *
	 * @param string $name The property name.
	 * @return boolean
	 */
	public function __isset($name)
	{
		if(!isset($this->_data[$name]))
		{
			// Lazy-load the data from the hook.
			if(isset($this->_hook) && !$this->_hookReadData)
			{
				$this->_hook->createPageInfo($this);
				$this->_hookReadData = true;
				// If still does not exist, the name is invalid.
				if(!isset($this->_data[$name]))
				{
					return false;
				}
			}
			else
			{
				return false;
			}
		}
		return true;
	} // end __isset();

	/**
	 * Returns the parent node object.
	 * 
	 * @return Page
	 */
	public function getParent()
	{
		return $this->_parent;
	} // end getParent();

	/**
	 * Returns the next brother object.
	 *
	 * @return Page
	 */
	public function getNext()
	{
		return $this->_next;
	} // end getNext();

	/**
	 * Returns the previous brother object.
	 *
	 * @return Page
	 */
	public function getPrev()
	{
		return $this->_prev;
	} // end getPrev();

	/**
	 * Returns the first child of the current node. If a hook has been defined,
	 * the method may lazy-load the children.
	 *
	 * @return Page
	 */
	public function getFirstChild()
	{
		if(is_object($this->_hook) && !$this->_hookReadChildren)
		{
			$this->_hook->createPageChildren($this);
			$this->_hookReadChildren = true;
		}
		return $this->_firstChild;
	} // end getFirstChild();

	/**
	 * Returns the last child of the current node. If a hook has been defined,
	 * the method may lazy-load the children.
	 *
	 * @return Page
	 */
	public function getLastChild()
	{
		if(is_object($this->_hook) && !$this->_hookReadChildren)
		{
			$this->_hook->createPageChildren($this);
			$this->_hookReadChildren = true;
		}
		return $this->_lastChild;
	} // end getLastChild();

	/**
	 * Returns true, if there are children defined. If a hook has been defined,
	 * the method may lazy-load the children.
	 *
	 * @return boolean
	 */
	public function hasChildren()
	{
		if(is_object($this->_hook) && !$this->_hookReadChildren)
		{
			$this->_hook->createPageChildren($this);
			$this->_hookReadChildren = true;
		}
		return $this->_firstChild !== null;
	} // end getLastChild();

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
		$this->_iterator = $this->_firstChild;
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
			$this->_iterator = $this->_firstChild;
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
