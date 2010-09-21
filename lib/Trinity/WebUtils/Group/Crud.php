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
namespace Trinity\WebUtils\Group;
use \Trinity\Model\Interfaces\Grid as Interface_Grid;
use \Trinity\Web\Controller_Exception;
use \Trinity\Web\Redirect_Exception;
use \Trinity\Web\Redirect_Flash;
use \Trinity\Web\Controller\Manager;
use \Trinity\WebUtils\View\Grid as View_Grid;
use \Trinity\WebUtils\View\Form as View_Form;
use \Trinity\WebUtils\View\Question as View_Question;
use \Trinity\WebUtils\Controller\Action_Group as Controller_Action_Group;


/**
 * The universal CRUD manager.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
abstract class Crud extends Controller_Action_Group
{
	/**
	 * Here we can define custom templates.
	 * @var array
	 */
	public $templates = array(
		'index' => null,
		'add' => null,
		'edit' => null,
		'remove' => null
	);

	/**
	 * The CRUD initializer. It should return the model used by the CRUD.
	 *
	 * @param \Trinity\Web\Controller\Manager $manager The controller manager
	 * @return \Trinity\Basement\Model
	 */
	abstract public function initCrud(Manager $manager);

	/**
	 * Should provide forms.
	 *
	 * @param \Trinity\Web\Controller\Manager $manager The controller manager
	 * @param string $type The form type to return
	 * @return \Opf_Form
	 */
	abstract public function getForm(Manager $manager, $type);

	/**
	 * Tests the CRUD contract.
	 *
	 * @param \Trinity\Web\Controller\Manager $manager The controller manager
	 * @param string $actionInterface The action interface name.
	 * @return \Trinity\Basement\Model
	 */
	protected function _getCrud(Manager $manager, $actionInterface = null)
	{
		$model = $this->initCrud($manager);

		if(!$model instanceof Interface_Grid)
		{
			throw new Controller_Exception('The returned model does not implement Grid interface.');
		}
		if($actionInterface !== null && !is_a($model, $actionInterface))
		{
			$router = $this->getService('web.Router');
			throw new Redirect_Exception($router->assemble(array('action' => 'index')));
		}
		return $model;
	} // end _getCrud();

	/**
	 * The index action displays a list of rows.
	 *
	 * @param \Trinity\Web\Controller\Manager $manager The controller manager
	 * @return View
	 */
	public function indexAction(Manager $manager)
	{
		$model = $this->_getCrud($manager);
		$view = $manager->getView('Trinity.WebUtils.View.Grid');

		if($this->templates['index'] !== null)
		{
			$view->setTemplateName('default', $this->templates['index']);
		}

		$view->addModel('grid', $model);
		$view->set('title', $model->getMessage('crud.title'));
		$view->set('page', $manager->request->getParam('page', 1));
		return $view;
	} // end indexAction();

	/**
	 * This action processes the row adding.
	 *
	 * @param \Trinity\Web\Controller\Manager $manager The controller manager
	 * @return View
	 */
	public function addAction(Manager $manager)
	{
		$model = $this->_getCrud($manager, '\\Trinity\\Model\\Interfaces\\Addable');
		$form = $this->getForm($manager, 'add');
		$form->setName('add');

		if($form->execute() == \Opf_Form::ACCEPTED)
		{
			$router = $manager->services->get('web.Router');
			$flashMessage = '';
			try
			{
				$model->addItem($form->getValue());
				$flashMessage = $model->getMessage('crud.message.added');
			}
			catch(\Trinity\Model\Report $report)
			{
				$flashMessage = $report->getMessage();
			}
			throw new Redirect_Flash($router->assemble(array('action' => 'index'), null, true),
				$flashMessage
			);
		}
		else
		{
			$view = $manager->getView('Trinity.WebUtils.View.Form');
			if($this->templates['add'] !== null)
			{
				$view->setTemplateName('default', $this->templates['add']);
			}
			$view->set('title', $model->getMessage('crud.add'));
			$view->addModel('form', $form);
			return $view;
		}
	} // end addAction();

	/**
	 * This action processes the row editing.
	 *
	 * @param \Trinity\Web\Controller\Manager $manager The controller manager
	 * @return View
	 */
	public function editAction(Manager $manager)
	{
		$model = $this->_getCrud($manager, '\\Trinity\\Model\\Interfaces\\Editable');
		try
		{
			$model->id = $manager->request->getParam('id');
			$form = $this->getForm($manager, 'edit');
			$form->setName('edit');

			if($form->execute() == \Opf_Form::ACCEPTED)
			{
				$model->editItem($form->getValue());

				throw new Redirect_Flash($manager->router->assemble(array('action' => 'index'), null, true),
					$model->getMessage('crud.message.edited')
				);
			}
			else
			{
				$view = $manager->getView('Trinity.WebUtils.View.Form');
				if($this->templates['add'] !== null)
				{
					$view->setTemplateName('default', $this->templates['edit']);
				}
				$view->set('title', $model->getMessage('crud.edit'));

				$form->populate($model->getItemForEdit());

				$view->addModel('form', $form);
				return $view;
			}
		}
		catch(\Trinity\Model\Report $report)
		{
			throw new Redirect_Flash($manager->router->assemble(array('action' => 'index'), null, true),
				$report->getMessage(),
				'error'
			);
		}
	} // end editAction();

	/**
	 * This action processes the row removal.
	 *
	 * @param \Trinity\Web\Controller\Manager $manager The controller manager
	 * @return View
	 */
	public function removeAction(Manager $manager)
	{
		$model = $this->_getCrud($manager, '\\Trinity\\Model\\Interfaces\\Removable');
		$model->id = $manager->request->getParam('id');
		$answer = $manager->request->getParam('answer');
		switch((string)$answer)
		{
			case 'yes':
				try
				{
					$model->removeItem();
					$flashMessage = $model->getMessage('crud.message.removed');
				}
				catch(\Trinity\Model\Report $report)
				{
					$flashMessage = $report->getMessage();
				}
				throw new Redirect_Flash($manager->router->assemble(array('action' => 'index'), null, true),
					$flashMessage
				);
				break;
			case 'no':
				throw new Redirect_Exception($manager->router->assemble(array('action' => 'index'), null, true));
			default:
				$view = $manager->getView('Trinity.WebUtils.View.Question');
				$view->set('title', $model->getMessage('crud.remove'));
				$view->addModel('item', $model);
				return $view;
				break;
		}
	} // end removeAction();
} // end Crud;