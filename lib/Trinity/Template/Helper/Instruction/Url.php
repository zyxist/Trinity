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
namespace Trinity\Template\Helper\Instruction;
use \Opt_Instruction_Abstract;
use \Opt_Xml_Node;
use \Opt_Xml_Buffer;
use \Opt_Xml_Attribute;
use \Opt_Instruction_Exception;
use \Opt_Instruction_Attribute;
use \Opt_Compiler_Class;

class Url extends Opt_Instruction_Abstract
{
	protected $_name = 'Url';
	protected $_defaultArea;

    public function configure()
    {
        $this->_addInstructions(array('trinity:url'));
        $this->_addAttributes(array('trinity:url'));
		$area = \Trinity\Basement\Application::getApplication()->getServiceLocator()->get('web.Area');
		$this->_defaultArea = $area->getName();
    } // end configure();

	public function processNode(Opt_Xml_Node $node)
	{
		// TODO: Improve checking parents.
		// Prevent from adding an attribute to OPT tags
		/*if(!$node->getParent() instanceof Opt_Xml_Element)
		{
			throw new Opt_Instruction_Exception($node->getXmlName(), 'any non-OPT tag');
		}
		if($this->_compiler->isNamespace($node->getParent()->getNamespace()))
		{
			throw new Opt_Instruction_Exception($node->getXmlName(), 'any non-OPT tag');
		}*/

		// Parse the instruction attributes
		$params = array(
			'attribute' => array(0 => self::OPTIONAL, self::STRING, 'href'),
			'area' => array(0 => self::OPTIONAL, self::STRING, $this->_defaultArea),
			'__UNKNOWN__' => array(0 => self::OPTIONAL, self::EXPRESSION)
		);
		$vars = $this->_extractAttributes($node, $params);

		if($node->hasChildren())
		{
			$attributes = $node->getElementsByTagNameNS('opt', 'attribute', false);
			if(sizeof($attributes) == 0)
			{
				$this->_buildCode($node, $vars, $params['attribute']);
				return;
			}
			foreach($attributes as $attr)
			{
				$attr->set('attributeValueStyle', Opt_Instruction_Attribute::ATTR_RAW);
			}
			$node->set('priv:url', $vars);
			$node->set('priv:attribute', $params['attribute']);
			$node->set('postprocess', true);
			$this->_process($node);
		}
		else
		{
			$this->_buildCode($node, $vars, $params['attribute']);
		}
	} // end processNode();
	
	/**
	 * Processes the trinity:url attributes.
	 * @internal
	 * @param Opt_Xml_Node $node The node with the attribute
	 * @param Opt_Xml_Attribute $attr The recognized attribute.
	 */
	public function processAttribute(Opt_Xml_Node $node, Opt_Xml_Attribute $attr)
	{
		if(!$this->_compiler->isNamespace($node->getNamespace()))
		{
			//
		}
		$expression = $this->_compiler->compileExpression('['.$attr->getValue().']', false, Opt_Compiler_Class::ESCAPE_OFF);
		//var_dump($expression); exit;
		$code = 'echo \Trinity\Basement\Application::getApplication()->getServiceLocator()->get(\'helper.Url\')->assemble('.$expression[0].');';
		// Create an attribute for the parent.
		$attribute = new Opt_Xml_Attribute('href', null);
		$attribute->addAfter(Opt_Xml_Buffer::ATTRIBUTE_VALUE, $code);

		$node->addAttribute($attribute);
	} // end processAttribute();

	private function _buildCode(Opt_Xml_Node $node, array $vars, $attributeName)
	{
		// Build the code
		$code = 'echo \Trinity\Basement\Application::getApplication()->getServiceLocator()->get(\'helper.Url\')->assemble(array(';
		foreach($vars as $name => $value)
		{
			$code .= '\''.$name.'\' => '.$value.',';
		}
		$code .= ')); ';

		// Create an attribute for the parent.
		$attribute = new Opt_Xml_Attribute($attributeName, null);
		$attribute->addAfter(Opt_Xml_Buffer::ATTRIBUTE_VALUE, $code);

		$node->getParent()->addAttribute($attribute);
	} // end _buildCode();
} // end Url;