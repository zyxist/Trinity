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
namespace Trinity\Utils;
use \Trinity\Basement\Service as Service;
use \Opc_Class;

/**
 * The Open Power Forms instance builder.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
class Service_Opc extends Service
{
	/**
	 * List of services to preload.
	 * @return array
	 */
	public function toPreload()
	{
		return array('utils.Config');
	} // end toPreload();

	/**
	 * Preconfigures and initializes the OPF object.
	 *
	 * @return \Opc_Class
	 */
	public function getObject()
	{
		$config = $this->_serviceLocator->get('utils.Config');

		// Create the OPF instance.
		$opc = new Opc_Class();
		$opc->paginatorDecorator = $config->pagination->decorator;
		$opc->paginatorDecoratorOptions = $config->pagination->decoratorOpts->toArray();
		$opc->itemsPerPage = $config->pagination->itemsPerPage;

		return $opc;	
	} // end getObject();
} // end Service_Opc;