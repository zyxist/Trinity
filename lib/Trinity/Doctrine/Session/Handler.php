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
namespace Trinity\Doctrine\Session;
use \Trinity\Web\Session\Handler as Web_Session_Handler;

/**
 * The session handler that uses Doctrine 2 to store sessions in
 * the database.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 * @todo implement
 */
class Handler implements Web_Session_Handler
{
	/**
	 * A method called for opening a session and retrieving
	 * resources.
	 *
	 * @param string $savePath Session save path
	 * @param string $name Session name
	 */
	public function open($safePath, $name)
	{

	} // end open();

	/**
	 * Reads the session data with the specified ID.
	 *
	 * @param string $id Session identifier
	 * @return array
	 */
	public function read($id)
	{

	} // end read();

	/**
	 * Saves the session data under the specified id.
	 * @param string $id Session identifier
	 * @param array $data Session data
	 */
	public function write($id, $data)
	{

	} // end write();

	/**
	 * Closes the session.
	 */
	public function close()
	{

	} // end close();

	/**
	 * Performs a garbage collection of the old sessions.
	 *
	 * @param int $maxLifetime Maximum session lifetime
	 */
	public function gc($maxLifetime)
	{

	} // end gc();

	/**
	 * Destroys session with the specified id.
	 *
	 * @param string $id Session identifier
	 */
	public function destroy($id)
	{

	} // end destroy();
} // end Handler;