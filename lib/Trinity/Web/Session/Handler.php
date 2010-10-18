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
namespace Trinity\Web\Session;

/**
 * Interface for writing custom session handlers.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
interface Handler
{
	/**
	 * A method called for opening a session and retrieving
	 * resources.
	 * 
	 * @param string $savePath Session save path
	 * @param string $name Session name
	 */
	public function open($safePath, $name);

	/**
	 * Reads the session data with the specified ID.
	 *
	 * @param string $id Session identifier
	 * @return array
	 */
	public function read($id);

	/**
	 * Saves the session data under the specified id.
	 * @param string $id Session identifier
	 * @param array $data Session data
	 */
	public function write($id, $data);

	/**
	 * Closes the session.
	 */
	public function close();

	/**
	 * Performs a garbage collection of the old sessions.
	 *
	 * @param int $maxLifetime Maximum session lifetime
	 */
	public function gc($maxLifetime);

	/**
	 * Destroys session with the specified id.
	 *
	 * @param string $id Session identifier
	 */
	public function destroy($id);
} // end Session_Handler;