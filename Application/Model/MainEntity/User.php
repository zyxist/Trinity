<?php
/**
 * The testing application for Trinity Framework
 *
 * @author Tomasz Jędrzejewski
 */


namespace MainEntity;

/**
 * @Entity
 * @Table(name="user")
 */
class User
{
	/**
	 * @Id
	 * @Column(type="integer")
	 * @GeneratedValue
	 */
	private $id;
	/** @Column(type="string") */
	private $name;
	/** @Column(type="integer") */
	private $age;
} // end User;