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
use \Trinity\Web\View as WebView;
use \Trinity\Web\View_Html as View_Html;
use \Trinity\Web\Controller_Exception as Web_Controller_Exception;

/**
 * This view represents a grid table of rows imported from a
 * model. The models may define column headers, specify sorting
 * rules, pagination and filtering rules.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
class Grid extends View_Html
{

	/**
	 * Creates the grid view.
	 * 
	 * @param BaseApplication $application Application link.
	 */
	public function __construct(BaseApplication $application)
	{
		parent::__construct($application);
		$this->setTemplate('app.templates:grid.tpl');
	} // end __construct();

	/**
	 * Dispatches the view.
	 */
	public function dispatch()
	{
		$view = $this->getTemplateObject();

		$model = $this->getModel('grid', '\\Trinity\\Model\\Interface_Grid');

		$view->title = $this->get('title');
		$view->headers = $model->getColumnHeaders();
		$view->items = $items = $model->getItems();

		if(sizeof($items) == 0)
		{
			$view->noDataMessage = $model->getMessage('noData');
		}

		$layout = $this->_application->getServiceLocator()->get('template.Layout');
		$layout->appendView($view);
	} // end dispatch();

} // end Grid;