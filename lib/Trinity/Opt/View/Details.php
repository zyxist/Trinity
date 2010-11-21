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

/**
 * This view represents a grid table of rows imported from a
 * model. The models may define column headers, specify sorting
 * rules, pagination and filtering rules.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
class Details extends Html
{
	/**
	 * Constructs the view.
	 *
	 * @param \Trinity\Basement\ServiceLocator $serviceLocator The service locator
	 */
	public function __construct(ServiceLocator $serviceLocator)
	{
		parent::__construct($serviceLocator);
		$this->setTemplateName('default', 'application.templates:details.tpl');
	} // end __construct();

	/**
	 * Dispatches the view.
	 */
	public function dispatch()
	{
		$view = $this->templateFactory();

		$model = $this->getModel('details', '\\Trinity\\WebUtils\\Model\\Interfaces\\Details');
		$translation = $this->_serviceLocator->get('Translation');

		$view->setFormat('category', 'AssociativeArray');
		$view->setFormat('item', 'AssociativeArray');

		$brief = $model->getBriefInformation();
		$view->title = $brief['title'];

		$categories = $model->getAvailableDetailCategories();
		$data = array();
		$name = $model->myName();
		foreach($categories as $category)
		{
			$data[$category] = array(
				'title' => $translation->_($name, 'details.category.'.$category),
				'item' => $model->getDetailCategory($category)
			);
			$view->$category = $data[$category];
		}
		$view->category = $data;

		// Display the layout
		$layout = $this->_serviceLocator->get('Layout');
		$layout->appendView($view);
	} // end dispatch();
} // end Details;