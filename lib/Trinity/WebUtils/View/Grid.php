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
use \Trinity\Model\Interfaces\Previewable as Interface_Previewable;
use \Trinity\Model\Interfaces\Addable as Interface_Addable;
use \Trinity\Model\Interfaces\Editable as Interface_Editable;
use \Trinity\Model\Interfaces\Removable as Interface_Removable;
use \Trinity\Model\Interfaces\Movable as Interface_Movable;
use \Trinity\Model\Interfaces\Paginable as Interface_Paginable;
use \Opc_Paginator;

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
		$this->setTemplateName('default', 'app.templates:grid.tpl');
	} // end __construct();

	/**
	 * Dispatches the view.
	 */
	public function dispatch()
	{
		$view = $this->templateFactory();

		$model = $this->getModel('grid', '\\Trinity\\Model\\Interfaces\\Grid');

		// Add pagination
		if($model instanceof Interface_Paginable)
		{
			$paginator = Opc_Paginator::create($model->count());
			$paginator->page = $this->get('page');

			$model->setLimit($paginator->limit, $paginator->offset);
			$view->paginator = $paginator;
			$view->setFormat('paginator', 'Objective/Array');
			$view->setFormat('paginator.decorator', 'Objective');
		}

		// Get the rows
		$view->title = $this->get('title');
		$view->headers = $model->getColumnHeaders();
		$view->items = $items = $model->getItems();

		if(sizeof($items) == 0)
		{
			$view->noDataMessage = $model->getMessage('grid.no-data');
		}

		// Check extra stuff
		if($model instanceof Interface_Previewable)
		{
			$view->previewAction = true;
		}
		if($model instanceof Interface_Addable)
		{
			$view->addAction = true;
		}
		if($model instanceof Interface_Editable)
		{
			$view->editAction = true;
		}
		if($model instanceof Interface_Removable)
		{
			$view->removeAction = true;
		}
		if($model instanceof Interface_Movable)
		{
			$view->moveActions = true;
		}

		// Display the layout
		$layout = $this->_application->getServiceLocator()->get('template.Layout');
		$layout->appendView($view);
	} // end dispatch();

} // end Grid;