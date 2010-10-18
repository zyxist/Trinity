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
namespace Trinity\WebUtils\Helper;
use \Trinity\Web\Session\Group as Session_Group;

/**
 * The flash helper for controllers.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
class Flash
{
	/**
	 * The session group used to store flash messages.
	 * @var \Trinity\Web\Session\Group
	 */
	protected $_sessionGroup = null;

	/**
	 * Creates the flash helper.
	 * 
	 * @param Session_Group $sessionGroup The session group.
	 */
	public function __construct(Session_Group $sessionGroup)
	{
		$this->_sessionGroup = $sessionGroup;
	} // end __construct();

	/**
	 * Adds a new flash message. Implements fluent interface.
	 *
	 * @param string $message The message.
	 * @param string $type The optional message type.
	 * @return Flash
	 */
	public function addMessage($message, $type = 'standard')
	{
		$idx = $this->_sessionGroup->count();
		$this->_sessionGroup->{$idx} = array(
			'message' => $message,
			'type' => $type
		);
		$this->_sessionGroup->setLifetime($idx, 1);
	} // end addMessage();

	/**
	 * Returns true, if there are any flash messages defined.
	 *
	 * @return boolean
	 */
	public function hasMessages()
	{
		return $this->_sessionGroup->count() > 0;
	} // end hasMessages();

	/**
	 * Returns the message list.
	 *
	 * @return array
	 */
	public function getMessages()
	{
		$list = array();
		foreach($this->_sessionGroup as $message)
		{
			$list[] = $message;
		}
		return $list;
	} // end getMessages();

	/**
	 * Returns the session group used by this helper.
	 *
	 * @return \Trinity\Web\Session\Group
	 */
	public function getSessionGroup()
	{
		return $this->_sessionGroup;
	} // end getSessionGroup();
} // end Flash;