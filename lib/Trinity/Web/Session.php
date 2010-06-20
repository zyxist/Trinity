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
use Trinity\Basement\Application as BaseApplication;

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
	 * The list of loaded session namespaces.
	 * @var array
	 */
	private $_namespaces = array();

	/**
	 * Has the session been started?
	 * @var boolean
	 */
	private $_started = false;

	/**
	 * The application link.
	 * @var BaseApplication
	 */
	private $_application;

	/**
	 * Creates the session object.
	 *
	 * @param BaseApplication $application The application link.
	 * @param Session_Handler $handler The session handler used for the session
	 *        management.
	 */
	public function __construct(BaseApplication $application, Session_Handler $handler = null)
	{
		if($handler !== null)
		{
			$this->setSessionHandler($handler);
		}
		$this->_application = $application;
	} // end __construct();

	public function getNamespace($namespace)
	{
		if(!isset($this->_namespaces[$namespace]))
		{
			$this->_createNamespace($namespace);
		}
		return $this->_namespaces[$namespace];
	} // end getNamespace();

	public function hasNamespace($namespace)
	{
		return isset($this->_namespaces[$namespace]);
	} // end hasNamespace();

	public function removeNamespace($namespace)
	{
		if(!isset($this->_namespaces[$namespace]))
		{
			throw new Session_Exception('The namespace '.$namespace.' does not exist.');
		}
		$this->_namespaces[$namespace]->remove();
		unset($this->_namespaces[$namespace]);
	} // end removeNamespace();

	public function getSessionHandler($handler)
	{
		return $this->_handler;
	} // end getSessionHandler();

	public function setSessionHandler($handler)
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

			$this->_application->getEventManager()->fire('web.session.start', array('session' => $this));

			return true;
		}
		return false;
	} // end start();

	public function writeClose()
	{
		$this->_application->getEventManager()->fire('web.session.close', array('session' => $this));

		session_write_close();
	} // end writeClose();

	protected function _createNamespace($name)
	{
		$this->_namespaces[$name] = new Session_Namespace($name);

		$this->_application->getEventManager()->fire('web.session.namespace-created', array('session' => $this, 'namespace' => $this->_namespaces[$name]));
	} // end _createNamespace();

	protected function _initializeEmptySession()
	{
		session_regenerate_id();

		// Clear the whole state
		$_SESSION = array();
		$_SESSION[':initialized'] = true;
		$_SESSION[':ip'] = $_SERVER['REMOTE_ADDR'];
		$_SESSION[':browser'] = (isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'not-set');

		$this->_application->getEventManager()->fire('web.session.initialized', array('session' => $this));
	} // end _initializeEmptySession();
} // end Session;