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
namespace Trinity\Opt\Helper;
use Trinity\Opt\Helper\Container;
use \Opt_Function;

/**
 * A container for various scripts
 *
 * @copyright Copyright (c) Invenzzia Group 2009
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
class Script extends Container
{
	/**
	 * Accepted attributes
	 * @var Array
	 */
	protected $_acceptedAttributes = array('type', 'charset', 'defer', 'language', 'src');
	/**
	 * Prepends a new external script file to the script list. The file is the
	 * remote URL to the script.
	 *
	 * @param String $file Remote URL to the script file
	 * @param Boolean $local Is file on local server?
	 * @param String $type Content type
	 * @param Array $options Extra options
	 */
	public function prependFile($file, $type = 'text/javascript', $options = array())
	{
		$options['src'] = $file;
		$options['type'] = $type;
		$row = array(
			'item' => 'file',
			'file' => $file,
			'attributes' => $this->_filterAttributes($options)
		);
		$this->_prepend('file:'.$file, $row);
	} // end prependFile();

	/**
	 * Appends a new external script file to the script list. The file is the
	 * remote URL to the script.
	 *
	 * @param String $file Remote URL to the script file
	 * @param Boolean $local Is file on local server?
	 * @param String $type Content type
	 * @param Array $options Extra options
	 */
	public function appendFile($file, $type = 'text/javascript', $options = array())
	{
		$options['src'] = $file;
		$options['type'] = $type;
		$row = array(
			'item' => 'file',
			'file' => $file,
			'attributes' => $this->_filterAttributes($options)
		);
		$this->_append('file:'.$file, $row);
	} // end appendFile();

	/**
	 * Sets a new external script file in the specified location of the script list.
	 * The file is the remote URL to the script.
	 *
	 * @param Integer $offset The offset
	 * @param String $file Remote URL to the script file
	 * @param Boolean $local Is file on local server?
	 * @param String $type Content type
	 * @param Array $options Extra options
	 */
	public function offsetSetFile($offset, $file, $type = 'text/javascript', $options = array())
	{
		$options['src'] = $file;
		$options['type'] = $type;
		$row = array(
			'item' => 'file',
			'file' => $file,
			'attributes' => $this->_filterAttributes($options)
		);
		$this->_offsetSet($offset, 'file:'.$file, $row);
	} // end offsetSetFile();

	/**
	 * Prepends a new script to the script list.
	 *
	 * @param String $script The script source
	 * @param String $type Content type
	 * @param Array $options Extra options
	 */
	public function prependScript($script, $type = 'text/javascript', $options = array())
	{
		if(isset($options['src']))
		{
			unset($options['src']);
		}
		$options['type'] = $type;
		$row = array(
			'item' => 'script',
			'script' => $script,
			'attributes' => $this->_filterAttributes($options)
		);
		$this->_prepend(null, $row);
	} // end prependScript();

	/**
	 * Appends a new script to the script list.
	 *
	 * @param String $script The script source
	 * @param String $type Content type
	 * @param Array $options Extra options
	 */
	public function appendScript($script, $type = 'text/javascript', $options = array())
	{
		if(isset($options['src']))
		{
			unset($options['src']);
		}
		$options['type'] = $type;
		$row = array(
			'item' => 'script',
			'script' => $script,
			'attributes' => $this->_filterAttributes($options)
		);
		$this->_append(null, $row);
	} // end appendScript();

	/**
	 * Sets a new script in the specified location of the script list.
	 *
	 * @param String $script The script source
	 * @param String $type Content type
	 * @param Array $options Extra options
	 */
	public function offsetSetScript($offset, $script, $type = 'text/javascript', $options = array())
	{
		if(isset($options['src']))
		{
			unset($options['src']);
		}
		$options['type'] = $type;
		$row = array(
			'item' => 'script',
			'script' => $script,
			'attributes' => $this->_filterAttributes($options)
		);
		$this->_offsetSet(null, $row);
	} // end offsetSetScript();

	/**
	 * Prepends a new template script group to the script list. It can be used only
	 * with template-based rendering.
	 *
	 * @param String $group The group name
	 * @param Array $options Extra options
	 */
	public function prependGroup($group, $options = array())
	{
		if($group == 'file' || $group == 'script')
		{
			throw new Exception('The "'.$group.'" is a reserved group name and cannot be used.');
		}
		$options['item'] = $group;
		$this->_prepend('group:'.$group, $options);
	} // end prependGroup();

	/**
	 * Appends a new template script group to the script list. It can be used only
	 * with template-based rendering.
	 *
	 * @param String $group The group name
	 * @param Array $options Extra options
	 */
	public function appendGroup($group, $options = array())
	{
		if($group == 'file' || $group == 'script')
		{
			throw new Exception('The "'.$group.'" is a reserved group name and cannot be used.');
		}
		$options['item'] = $group;
		$this->_append('group:'.$group, $options);
	} // end appendGroup();

	/**
	 * Sets a new template group in the specified location of the script list.
	 * It can be used only with template-based rendering.
	 *
	 * @param String $group The group name
	 * @param Array $options Extra options
	 */
	public function offsetSetGroup($offset, $group, $options = array())
	{
		if($group == 'file' || $group == 'script')
		{
			throw new Exception('The "'.$group.'" is a reserved group name and cannot be used.');
		}
		$options['item'] = $group;
		$this->_offsetSet($offset, 'group:'.$group, $options);
	} // end offsetSetGroup();

	public function __toString()
	{
		$output = '';
		foreach($this->_elements as $element)
		{
			switch($element['item'])
			{
				case 'file':
					$output .= '<script '.Opt_Function::buildAttributes($element['attributes'])."></script>\r\n";
					break;
				case 'script':
					$output .= '<script '.Opt_Function::buildAttributes($element['attributes']).'>/* <![CDATA[ */ '.$element['script']." /* ]]> */</script>\r\n";
					break;
				default:
					break;
			}
		}
		return $output;
	} // end __toString();
} // end Script;