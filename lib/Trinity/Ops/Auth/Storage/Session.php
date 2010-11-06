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
namespace Trinity\Ops\Auth\Storage;
use \Ops\Auth\Storage;
use \Trinity\Web\Session as Web_Session;
use \Trinity\WebUtils\Model\Interfaces\PersistentIdentity as Interface_PersistentIdentity;

/**
 * The Trinity session storage engine for the OPS authentication.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
class Session implements Storage
{
	/**
	 * The auth namespace.
	 * @var \Trinity\Web\Session\Group;
	 */
	private $_group;

	/**
	 * The persistent identity model.
	 * @var \Trinity\WebUtils\Model\Interface_PersistentIdentity;
	 */
	private $_persistentIdentity;

	/**
	 * Constructs the session storage object.
	 * 
	 * @param Session $session
	 */
	public function __construct(Web_Session $session, Interface_PersistentIdentity $model)
	{
		$this->_group = $session->getGroup('Auth');
		$this->_persistentIdentity = $model;
	} // end __construct();

	public function clear()
	{
		$this->_group->userId = null;
		$this->_group->accountType = null;
	} // end clear();

	public function read()
	{
		try
		{
			return $this->_persistentIdentity->getPersistentIdentity($this->_group->userId, $this->_group->accountType);
		}
		catch(\Trinity\WebUtils\Model\Report\NotFound $report)
		{
			$this->clear();
			return null;
		}
	} // end read();

	public function write($identity)
	{
		if(isset($identity['id']) && isset($identity['accountType']) && $identity['id'] !== null && $identity['accountType'] !== null)
		{
			$this->_group->userId = $identity['id'];
			$this->_group->accountType = $identity['accountType'];
		}
	} // end write();

	public function isEmpty()
	{
		return ($this->_group->userId !== null);
	} // end isEmpty();
} // end Storage_Session;