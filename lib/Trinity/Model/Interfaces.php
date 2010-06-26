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
namespace Trinity\Model\Interfaces;

/**
 * The interface for displaying a grid of rows.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
interface Message
{
	/**
	 * Returns the message with the specified identifier.
	 *
	 * @param string $messageId The message identifier
	 * @return string
	 */
	public function getMessage($messageId);
} // end Message;

/**
 * The interface for displaying a grid of rows.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
interface Grid extends Message
{
	/**
	 * Returns the column headers.
	 */
	public function getColumnHeaders();

	/**
	 * Returns the items.
	 */
	public function getItems();
} // end Grid;

/**
 * Models implementing this interface can return simplified information
 * about a particular row. The simplified information should include:
 *  - id
 *  - title
 *  - entityName (i.e. if the model returns users, it should return "user")
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
interface Brief
{
	/**
	 * Returns brief information about a particular row.
	 * @return array
	 */
	public function getBriefInformation();
} // end Brief;

interface PersistentIdentity
{
	public function getPersistentIdentity($id, $accountType);
} // end PersistentIdentity;

interface Addable
{
	public function addItem($data);
} // end Addable;

interface Editable
{
	public function getItemForEdit();
	public function editItem($data);
} // end Editable;

interface Removable
{
	public function removeItem();
} // end Removable;

/**
 * The rows from this model can be moved up and down.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
interface Movable
{
	/**
	 * Move the row up, if possible.
	 */
	public function moveUp();

	/**
	 * Move the row down, if possible.
	 */
	public function moveDown();
} // end Movable;