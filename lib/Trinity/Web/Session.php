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
use \Symfony\Component\EventDispatcher\Event;
use \Symfony\Component\EventDispatcher\EventDispatcher;
use \Trinity\Web\Session\Exception as Session_Exception;
use \Trinity\Web\Session\Handler;
use \Trinity\Web\Session\Group;

/**
 * The session management class.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 * @todo Add session remembering for longer period of time.
 * @todo Add session configuration.
 */
class Session
{
	/**
	 * The session handler interface.
	 * @var Session_Handler
	 */
	private $_handler;

	/**
	 * The list of loaded session groups.
	 * @var array
	 */
	private $_groups = array();

	/**
	 * Has the session been started?
	 * @var boolean
	 */
	private $_started = false;

	/**
	 * The application link.
	 * @var \Symfony\Component\EventDispatcher\EventDispatcher;
	 */
	private $_eventDispatcher;

	/**
	 * Creates the session object.
	 *
	 * @param EventDispatcher $eventDispatcher The event dispatcher.
	 * @param Session_Handler $handler The session handler used for the session
	 *        management.
	 */
	public function __construct(EventDispatcher $eventDispatcher, Handler $handler = null)
	{
		if($handler !== null)
		{
			$this->setSessionHandler($handler);
		}
		$this->_eventDispatcher = $eventDispatcher;
	} // end __construct();

	/**
	 * Returns the session group with the given name. If the group does not
	 * exist, it is automatically created.
	 *
	 * @param string $group Group name
	 * @return \Trinity\Web\Session\Group
	 */
	public function getGroup($group)
	{
		if(!isset($this->_groups[$group]))
		{
			$this->_createGroup($group);
		}
		return $this->_groups[$group];
	} // end getGroup();

	/**
	 * Returns true, if a group with the given name exists.
	 * @param string $group Group name.
	 * @return boolean
	 */
	public function hasGroup($group)
	{
		return isset($this->_groups[$group]);
	} // end hasGroup();

	/**
	 * Removes the group from the session.
	 *
	 * @throws \Trinity\Web\Session\Exception
	 * @param string $group Group name.
	 */
	public function removeGroup($group)
	{
		if(!isset($this->_groups[$group]))
		{
			throw new Session_Exception('The session group '.$group.' does not exist.');
		}
		$this->_groups[$group]->remove();
		unset($this->_groups[$group]);
	} // end removeGroup();

	/**
	 * Returns the session handler used by this session.
	 *
	 * @param string $handler The session handler.
	 * @return \Trinity\Web\Session\Handler
	 */
	public function getSessionHandler($handler)
	{
		return $this->_handler;
	} // end getSessionHandler();

	/**
	 * Registers the session handler for this session.
	 *
	 * @param Handler $handler Session handler
	 * @return boolean
	 */
	public function setSessionHandler(Handler $handler)
	{
		$this->_handler = $handler;

		return session_set_save_handler(
			array($handler, 'open'),
			array($handler, 'close'),
			array($handler, 'read'),
			array($handler, 'write'),
			array($handler, 'destroy'),
			array($handler, 'gc')
		);
	} // end setSessionHandler();

	/**
	 * Starts the session and validates it against some basic attacks. Returns
	 * <tt>true</tt>, if the session has been initialized properly.
	 * 
	 * @return boolean
	 */
	public function start()
	{
		if(!$this->_started)
		{
			session_start();

			// Regenerate the session ID, avoiding session ID injection.
			if(!isset($_SESSION[':initialized']))
			{
				$this->_initializeEmptySession();
			}

			// Protect the session against session hijacking
			if($_SESSION[':ip'] != $_SERVER['REMOTE_ADDR'])
			{
				$this->_initializeEmptySession();
			}
			if($_SESSION[':browser'] != (isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'not-set'))
			{
				$this->_initializeEmptySession();
			}
			$this->_started = true;

			$this->_eventDispatcher->notify(new Event($this, 'web.session.start'));

			return true;
		}
		return false;
	} // end start();

	/**
	 * Finishes the session  and fires <tt>web.session.close</tt> event.
	 */
	public function writeClose()
	{
		$this->_eventDispatcher->notify(new Event($this, 'web.session.close'));

		session_write_close();
	} // end writeClose();

	/**
	 * Creates a new group and fires <tt>web.session.group-created</tt> event.
	 * @param string $name The name of the new group.
	 */
	protected function _createGroup($name)
	{
		$this->_groups[$name] = new Group($name);

		$this->_eventDispatcher->notify(new Event($this, 'web.session.group-created', array('group' => $this->_groups[$name])));
	} // end _createGroup();

	/**
	 * Initializes a new, empty session, and fires <tt>web.session.initialized</tt>
	 * event.
	 */
	protected function _initializeEmptySession()
	{
		session_regenerate_id();

		// Clear the whole state
		$_SESSION = array();
		$_SESSION[':initialized'] = true;
		$_SESSION[':ip'] = $_SERVER['REMOTE_ADDR'];
		$_SESSION[':browser'] = (isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'not-set');

		$this->_eventDispatcher->notify(new Event($this, 'web.session.initialized'));
	} // end _initializeEmptySession();
} // end Session;