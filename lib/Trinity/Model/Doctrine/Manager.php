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
namespace Trinity\Model;
use \Trinity\Basement\Application as BaseApplication;
use \Doctrine\ORM\EntityManager;
use \Doctrine\ORM\Mapping\Driver\AnnotationDriver;

/**
 * Doctrine manager provides some integration services for Doctrine DBAL
 * and ORM. It simplifies adding new entity repositories, proxy directories
 * and other stuff.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
class Doctrine_Manager
{
	/**
	 * The entity manager.
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $_entityManager;

	/**
	 * The application link.
	 * @var \Trinity\Basement\Application
	 */
	private $_application;

	/**
	 * Constructs the Doctrine manager.
	 * 
	 * @param BaseApplication $application The application link.
	 * @param EntityManager $entityManager Entity manager
	 */
	public function __construct(BaseApplication $application, EntityManager $entityManager)
	{
		$this->_application = $application;
		$this->_entityManager = $entityManager;
	} // end __construct();

	/**
	 * Returns the entity manager.
	 *
	 * @return \Doctrine\ORM\EntityManager
	 */
	public function getEntityManager()
	{
		return $this->_entityManager;
	} // end getEntityManager();

	/**
	 * Registers a new entity repository.
	 * 
	 * @param string $name The entity top-level namespace.
	 * @param string $path The path to the entities.
	 */
	public function addEntityRepository($name, $path)
	{
		$metadataDriver = $this->_entityManager->getConfiguration()->getMetadataDriverImpl();

		if($metadataDriver instanceof AnnotationDriver)
		{
			$metadataDriver->addPaths(array($path));
		}

		$loader = $this->_application->getLoader('default');

		if($loader instanceof \Opl_Loader)
		{
			$loader->addLibrary($name, $path);
		}
	} // end addEntityRepository();

	/**
	 * Registers a group of entity repositories from an array, where the
	 * key is the entity namespace, and value - the class path.
	 *
	 * @param array $list List of entity repositories.
	 */
	public function addEntityRepositories(array $list)
	{
		$metadataDriver = $this->_entityManager->getConfiguration()->getMetadataDriverImpl();
		$loader = $this->_application->getLoader('default');

		if($metadataDriver instanceof AnnotationDriver)
		{
			$metadataDriver->addPaths($list);
		}
		foreach($list as $name => $path)
		{
			if($loader instanceof \Opl_Loader)
			{
				$loader->addLibrary($name, $path);
			}
		}
	} // end addEntityRepositories();
} // end Doctrine_Manager;