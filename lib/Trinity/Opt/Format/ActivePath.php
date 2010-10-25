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
use \Opt_Format_Abstract;
use \Opt_Instruction_Section_Abstract;
/**
 * The data format for ActivePath navigation helpers.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 * @package Formats
 */
class ActivePath extends Opt_Format_Abstract
{
	/**
	 * The list of supported hook types.
	 * @var array
	 */
	protected $_supports = array(
		'section'
	);

	/**
	 * Data format properties as information for the
	 * caller.
	 *
	 * @var array
	 */
	protected $_properties = array(
		'section:item' => false,
		'section:item.assign' => false,
		'section:variable' => true,
		'section:variable.exists' => false
	);

	/**
	 * Build a PHP code for the specified hook name.
	 *
	 * @internal
	 * @param String $hookName The hook name
	 * @return String The output PHP code
	 */
	protected function _build($hookName)
	{
		switch($hookName)
		{
			// Initializes the section by obtaining the list of items to display
			case 'section:init':
				$section = $this->_getVar('section');

				if(!is_null($section['parent']))
				{
					$parent = Opt_Instruction_Section_Abstract::getSection($section['parent']);
					$parent['format']->assign('item', $section['from']);
					return '$_sect'.$section['name'].'_vals = '.$parent['format']->get('section:variable').'; ';
				}
				elseif(!is_null($section['datasource']))
				{
					return '$_sect'.$section['name'].'_vals = '.$section['datasource'].'; ';
				}
				else
				{
					$this->assign('item', $section['name']);
					return '$_sect'.$section['name'].'_vals = $ctx->_data[\''.$section['name'].'\']; ';
				}
			// The end of the section loop.
			case 'section:endLoop':
				return ' } ';
			// The condition that should test if the section is not empty.
			case 'section:isNotEmpty':
				$section = $this->_getVar('section');
				return 'is_object($_sect'.$section['name'].'_vals) && ($_sect'.$section['name'].'_vals instanceof \Trinity\Navigation\Helper\ActivePath) && (($_sect'.$section['name'].'_cnt = $_sect'.$section['name'].'_vals->count()) > 0)';
			// The code block after the condition
			case 'section:started':
				return '';
			// The code block before the end of the conditional block.
			case 'section:finished':
				return '';
			// The code block after the conditional block
			case 'section:done':
				return '';
			// The code block before entering the loop.
			case 'section:loopBefore':
				$section = $this->_getVar('section');
				if($section['order'] == 'desc')
				{
					return ' $_sect'.$section['name'].'_vals->setIteratorMode(\Trinity\Navigation\Helper\ActivePath::DESCENDING); ';
				}
				else
				{
					return ' $_sect'.$section['name'].'_vals->setIteratorMode(\Trinity\Navigation\Helper\ActivePath::ASCENDING); ';
				}
				return '';
			// The default loop for the ascending order.
			case 'section:startAscLoop':
				$section = $this->_getVar('section');
				return 'foreach($_sect'.$section['name'].'_vals as $_sect'.$section['name'].'_i => $_sect'.$section['name'].'_v){ ';
			// The default loop for the descending order.
			case 'section:startDescLoop':
				$section = $this->_getVar('section');
				return 'foreach($_sect'.$section['name'].'_vals as $_sect'.$section['name'].'_i => $_sect'.$section['name'].'_v){ ';			// Retrieving the whole section item.
			case 'section:item':
				$section = $this->_getVar('section');
				return '$_sect'.$section['name'].'_v';
			// Retrieving a variable from a section item.
			case 'section:variable':
				$section = $this->_getVar('section');
				if($this->isDecorating())
				{
					return '$_sect'.$section['name'].'_v'.$this->_decorated->get('item:item');
				}
				return '$_sect'.$section['name'].'_v->'.$this->_getVar('item');
			case 'section:variableAssign':
				return '';
			// Resetting the section to the first element.
			case 'section:reset':
				$section = $this->_getVar('section');
				return '$_sect'.$section['name'].'_vals->rewind();';
			// Moving to the next element.
			case 'section:next':
				$section = $this->_getVar('section');
				return '$_sect'.$section['name'].'_vals->next();';
			// Checking whether the iterator is valid.
			case 'section:valid':
				$section = $this->_getVar('section');
				return '$_sect'.$section['name'].'_vals->valid();';
			// Populate the current element
			case 'section:populate':
				$section = $this->_getVar('section');
				return '$_sect'.$section['name'].'_v = '.$this->_vals.'->current(); $_sect'.$section['name'].'_i = '.$this->_vals.'->key();';
			// The code that returns the number of items in the section;
			case 'section:count':
				$section = $this->_getVar('section');
				return '$_sect'.$section['name'].'_cnt';
			// Section item size.
			case 'section:size':
				$section = $this->_getVar('section');
				return '($_sect'.$section['name'].'_v instanceof Countable ? $_sect'.$section['name'].'_v->count() : -1)';
			// Section iterator.
			case 'section:iterator':
				$section = $this->_getVar('section');
				return '$_sect'.$section['name'].'_i';
			// Testing the first element.
			case 'section:isFirst':
				$section = $this->_getVar('section');
				if($section['order'] == 'asc')
				{
					return '($_sect'.$section['name'].'_i == 0)';
				}
				else
				{
					return '($_sect'.$section['name'].'_i == ($_sect'.$section['name'].'_cnt-1))';
				}
			// Testing the last element.
			case 'section:isLast':
				$section = $this->_getVar('section');
				if($section['order'] == 'asc')
				{
					return '($_sect'.$section['name'].'_i == ($_sect'.$section['name'].'_cnt-1))';
				}
				else
				{
					return '($_sect'.$section['name'].'_i == 0)';
				}
			// Testing the extreme element.
			case 'section:isExtreme':
				$section = $this->_getVar('section');
				return '(($_sect'.$section['name'].'_i == ($_sect'.$section['name'].'_cnt-1)) || ($_sect'.$section['name'].'_i == 0))';
		}
	} // end _build();
} // end ActivePath;