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
namespace Trinity\Opt\Format;
use \Opt_Format_SingleArray;

/**
 * The <tt>Flash</tt> data format for retrieving the data from the flash helper.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
class Flash extends Opt_Format_SingleArray
{
	/**
	 * Build a PHP code for the specified hook name.
	 *
	 * @internal
	 * @param String $hookName The hook name
	 * @return String The output PHP code
	 */
	protected function _build($hookName)
	{
		if($hookName == 'section:init')
		{
			$section = $this->_getVar('section');
			$this->assign('item', $section['name']);
			return '$_sect'.$section['name'].'_vals = &'.$this->get('variable:item').'->getMessages(); ';
		}
		else
		{
			return parent::_build($hookName);
		}
		return NULL;
	} // end _build();
} // end Flash;