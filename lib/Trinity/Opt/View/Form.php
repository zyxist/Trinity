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
 * This view renders a Open Power Forms form.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
class Form extends Html
{
	/**
	 * Constructs the view.
	 *
	 * @param \Trinity\Basement\ServiceLocator $serviceLocator The service locator
	 */
	public function __construct(ServiceLocator $serviceLocator)
	{
		parent::__construct($serviceLocator);
		$this->setTemplateName('default', 'application.templates:form.tpl');
	} // end __construct();

	/**
	 * Dispatches the view.
	 */
	public function dispatch()
	{
		$view = $this->templateFactory('default');

		$model = $this->getModel('form', '\\Opf\\Form\\Form');
		$model->setView($view);
		$model->render();

		$view->title = $this->get('title');
		$view->formName = $model->getName();

		$layout = $this->_serviceLocator->get('Layout');
		$layout->appendView($view);
	} // end dispatch();
} // end Form;