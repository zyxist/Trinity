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
use \Trinity\Basement\ServiceLocator as ServiceLocator;
use \Trinity\Web\Controller\Exception as Web_Controller_Exception;
use \Trinity\WebUtils\Model\Interfaces\Details as Interface_Details;
use \Trinity\WebUtils\Model\Interfaces\Addable as Interface_Addable;
use \Trinity\WebUtils\Model\Interfaces\Editable as Interface_Editable;
use \Trinity\WebUtils\Model\Interfaces\Removable as Interface_Removable;
use \Trinity\WebUtils\Model\Interfaces\Movable as Interface_Movable;
use \Trinity\WebUtils\Model\Interfaces\Paginable as Interface_Paginable;
use \Opc\Paginator;

/**
 * This view represents a grid table of rows imported from a
 * model. The models may define column headers, specify sorting
 * rules, pagination and filtering rules.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
class Grid extends Html
{
	/**
	 * Constructs the view.
	 *
	 * @param \Trinity\Basement\ServiceLocator $serviceLocator The service locator
	 */
	public function __construct(ServiceLocator $serviceLocator)
	{
		parent::__construct($serviceLocator);
		$this->setTemplateName('default', 'application.templates:grid.tpl');
	} // end __construct();

	/**
	 * Dispatches the view.
	 */
	public function dispatch()
	{
		$view = $this->templateFactory();

		$model = $this->getModel('grid', '\\Trinity\\WebUtils\\Model\\Interfaces\\Grid');
		$translation = $this->_serviceLocator->get('Translation');

		// Add pagination
		if($model instanceof Interface_Paginable)
		{
			$paginator = Paginator::create($this->_serviceLocator->get('Opc'), $model->count());
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
			$view->noDataMessage = $translation->_($model->myName(), 'grid.noData');
		}

		// Check extra stuff
		if($model instanceof Interface_Details)
		{
			$view->detailsAction = true;
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
		$layout = $this->_serviceLocator->get('Layout');
		$layout->appendView($view);
	} // end dispatch();

} // end Grid;