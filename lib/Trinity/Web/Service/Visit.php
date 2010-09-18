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
namespace Trinity\Web\Service;
use \Trinity\Basement\Service as Basement_Service;

/**
 * The visit builder.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
class Visit extends Basement_Service
{
	/**
	 * Preconfigures and initializes the visit object.
	 *
	 * @return \Opc_Visit
	 */
	public function getObject()
	{
		return new \Opc_Visit;
	} // end getObject();
} // end Visit;