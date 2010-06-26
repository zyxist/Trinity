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
namespace Trinity\Model\Doctrine;

/**
 * Because the Doctrine entity fields cannot be public, we must make our own
 * accessors that would allow to read and write to them. This could be very
 * annoying, so this entity class provides a generic access interface that
 * allows to avoid this problem without hundreds of "f**ks" addressed towards
 * Doctrine developers who invented such a limitation.
 */
class Entity
{
	/**
	 * Magic getter of the entity fields.
	 * @param string $name The field name
	 * @return mixed
	 */
	public function __get($name)
	{
		if($name[0] == '_')
		{
			return null;
		}
		return $this->$name;
	} // end __get();

	/**
	 * Magic setter for the entity fields.
	 */
	public function __set($name, $value)
	{
		if($name[0] != '_')
		{
			$this->$name = $value;
		}
	} // end __set();

	/**
	 * Extracts the entity data from an array.
	 *
	 * @param array $data The entity data.
	 */
	public function fromArray(array $data)
	{
		foreach($data as $name => $value)
		{
			if($name[0] != '_')
			{
				$this->$name = $value;
			}
		}
	} // end fromArray();
} // end Doctrine_Entity;