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

namespace Trinity\Opt\View;
use \Trinity\Basement\ServiceLocator;

/**
 * Creates a confirmation form.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
class Question extends Html
{
	/**
	 * Constructs the view.
	 *
	 * @param \Trinity\Basement\ServiceLocator $serviceLocator The service locator
	 */
	public function __construct(ServiceLocator $serviceLocator)
	{
		parent::__construct($serviceLocator);
		$this->setTemplateName('default', 'application.templates:question.tpl');
	} // end __construct();

	/**
	 * Dispatches the view.
	 */
	public function dispatch()
	{
		$view = $this->templateFactory();
		$translation = $this->_serviceLocator->get('Translation');
		$model = $this->getModel('item', '\\Trinity\\WebUtils\\Model\\Interfaces\\Brief');

		$data = $model->getBriefInformation();
		$translation->assign($model->myName(), 'crud.question', array($data['title']));
		$view->question = $translation->_($model->myName(), 'crud.question');

		$layout = $this->_serviceLocator->get('Layout');
		$layout->appendView($view);
	} // end dispatch();
} // end Question;