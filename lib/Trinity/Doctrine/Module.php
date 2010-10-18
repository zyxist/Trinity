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
namespace Trinity\Doctrine;
use \Trinity\Basement\Module as Basement_Module;

/**
 * The Doctrine module.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
class Module extends Basement_Module
{
	/**
	 * Returns the service container for this module.
	 * 
	 * @return Services
	 */
	public function registerServiceContainer()
	{
		return new Services;
	} // end registerServiceContainer();

	/**
	 * Registers a new entity repository in Doctrine. The repository is
	 * automatically registered in the autoloader.
	 * 
	 * @param string $name The entity repository name
	 * @param string $path The class path
	 */
	public function addEntityRepository($name, $path)
	{
		$serviceLocator = $this->getServiceLocator();
		$metadataDriver = $serviceLocator->get('EntityManager')->getConfiguration()->getMetadataDriverImpl();

		if($metadataDriver instanceof AnnotationDriver)
		{
			$metadataDriver->addPaths(array($path));
		}

		$loader = $serviceLocator->get('NamespaceLoader');
		if($loader instanceof \Opl_Loader)
		{
			$loader->addLibrary($name, $path);
		}
	} // end addEntityRepository();

	/**
	 * Registers a new collection of entity repositories in Doctrine. The
	 * repositories are automatically registered in the autoloader.
	 *
	 * @param array $list The associative array of pairs <tt>repository name => class path</tt>
	 */
	public function addEntityRepositories(array $list)
	{
		$serviceLocator = $this->getServiceLocator();
		$metadataDriver = $serviceLocator->get('EntityManager')->getConfiguration()->getMetadataDriverImpl();
		$loader = $serviceLocator->get('NamespaceLoader');

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
} // end Module;