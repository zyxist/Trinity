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
namespace Trinity\Utils\Auth;
use \Trinity\Model\Interfaces\PersistentIdentity as Interface_PersistentIdentity;
use \Trinity\Web\Session;
use \Ops\Auth\Storage;

/**
 * The Trinity session storage engine for the OPS authentication.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
class Storage_Session implements Storage
{
	/**
	 * The auth namespace.
	 * @var \Trinity\Web\Session_Namespace;
	 */
	private $_ns;

	/**
	 * The persistent identity model.
	 * @var \Trinity\Model\Interface_PersistentIdentity;
	 */
	private $_persistentIdentity;

	/**
	 * Constructs the session storage object.
	 * 
	 * @param Session $session
	 */
	public function __construct(Session $session, Interface_PersistentIdentity $model)
	{
		$this->_ns = $session->getNamespace('Auth');
		$this->_persistentIdentity = $model;
	} // end __construct();

	public function clear()
	{
		$this->_ns->userId = null;
		$this->_ns->accountType = null;
	} // end clear();

	public function read()
	{
		try
		{
			return $this->_persistentIdentity->getPersistentIdentity($this->_ns->userId, $this->_ns->accountType);
		}
		catch(\Trinity\Model\Report\NotFound $report)
		{
			$this->clear();
			return null;
		}
	} // end read();

	public function write($identity)
	{
		if(isset($identity['id']) && isset($identity['accountType']) && $identity['id'] !== null && $identity['accountType'] !== null)
		{
			$this->_ns->userId = $identity['id'];
			$this->_ns->accountType = $identity['accountType'];
		}
	} // end write();

	public function isEmpty()
	{
		return ($this->_ns->userId !== null);
	} // end isEmpty();
} // end Storage_Session;