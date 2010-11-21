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

namespace Trinity\Opt\Form;
use \Opc\Translate;
use \Opf\Form\Form;
use \Opf\Widget\Select;
use \Opf\Validator\Type;
use \Opf\Validator\GreaterThan;

/**
 * The selection form for the many-to-many relationship.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
class ManyToMany extends Form
{
	/**
	 * The data model
	 * @var object
	 */
	protected $_model;
	/**
	 * The translation interface
	 * @var \Opc\Translate
	 */
	protected $_translation;

	/**
	 * Sets the data model.
	 *
	 * @param object $model The data model
	 */
	public function setModel($model)
	{
		$this->_model = $model;
		return $this;
	} // end setModel();

	/**
	 * Sets the translation interface.
	 * 
	 * @param Translate $translation The translation interface.
	 * @return ManyToMany
	 */
	public function setTranslation(Translate $translation)
	{
		$this->_translation = $translation;

		return $this;
	} // end setTranslation();

	/**
	 * Initializes the data validation
	 */
	public function onInit()
	{
		// Define validators
		$this->addValidator('intType',
			new Type(Type::INTEGER, array('id'))
		);
		$this->addValidator('greaterThan',
			new GreaterThan(0, array('id'))
		);
		// Define fields
		$item = $this->itemFactory('id');
		$item->setRequired(true);
	} // end onInit();

	/**
	 * Initializes the form view.
	 */
	public function onRender()
	{
		$item = $this->itemFactory('id');
		$widget = new Select;
		$widget->setLabel($this->_translation->_($this->_model->myName(), 'manytomany.select'));
		$widget->setOptions($this->_model->listUnselected());
		$item->setWidget($widget);
	} // end onRender();
} // end Competition;