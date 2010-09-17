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

namespace Trinity\WebUtils\View;
use \Trinity\Basement\Application as BaseApplication;
use \Trinity\Web\View\Html as View_Html;
use \Trinity\Web\Controller_Exception as Web_Controller_Exception;

/**
 * Creates a confirmation form.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
class Question extends View_Html
{
	/**
	 * Creates the question view.
	 *
	 * @param BaseApplication $application Application link.
	 */
	public function __construct(BaseApplication $application)
	{
		parent::__construct($application);
		$this->setTemplate('app.templates:question.tpl');
	} // end __construct();

	/**
	 * Dispatches the view.
	 */
	public function dispatch()
	{
		$view = $this->getTemplateObject();
		$model = $this->getModel('item', '\\Trinity\\Model\\Interfaces\\Brief');
		$data = $model->getBriefInformation();
		$view->question = sprintf($model->getMessage('crud.question'), $data['title']);
		$layout = $this->_application->getServiceLocator()->get('template.Layout');
		$layout->appendView($view);
	} // end dispatch();
} // end Question;