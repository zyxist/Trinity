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
use \Trinity\Opt\Form\ManyToMany as SelectionForm;
use \Trinity\Web\Controller\Exception as Web_Controller_Exception;
use \Trinity\WebUtils\Model\Interfaces\ManyToMany as Interface_ManyToMany;
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
class ManyToMany extends Html
{
	/**
	 * Constructs the view.
	 *
	 * @param \Trinity\Basement\ServiceLocator $serviceLocator The service locator
	 */
	public function __construct(ServiceLocator $serviceLocator)
	{
		parent::__construct($serviceLocator);
		$this->setTemplateName('default', 'application.templates:manytomany.tpl');
	} // end __construct();

	/**
	 * Dispatches the view.
	 */
	public function dispatch()
	{
		$view = $this->templateFactory();

		$model = $this->getModel('list', '\\Trinity\\WebUtils\\Model\\Interfaces\\ManyToMany');
		$translation = $this->_serviceLocator->get('Translation');

		// Add pagination
		$paginator = Paginator::create($this->_serviceLocator->get('Opc'), $model->count());
		$paginator->page = $this->get('page');

		$model->setLimit($paginator->limit, $paginator->offset);
		$view->paginator = $paginator;
		$view->setFormat('paginator', 'Objective/Array');
		$view->setFormat('paginator.decorator', 'Objective');

		$info = $model->getBriefInformation();
		$translation->assign($model->myName(), 'manytomany.title', array($info['title']));

		// Get the rows
		$view->title = $translation->_($model->myName(), 'manytomany.title');
		$view->items = $model->listSubitems();
		$view->parentId = $model->parentId;

		if(sizeof($view->items) == 0)
		{
			$view->noDataMessage = $translation->_($model->myName(), 'manytomany.noData');
		}

		$form = $this->getModel('form', '\\Opf\\Form\\Form');
		$form->setView($view);
		$form->setModel($model);
		$form->setTranslation($translation);
		$form->render();
		$view->formName = $form->getName();

		// Display the layout
		$layout = $this->_serviceLocator->get('Layout');
		$layout->appendView($view);
	} // end dispatch();

} // end ManyToMany;