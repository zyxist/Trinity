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
namespace Trinity\Opt\Controller\Group;
use \Opf\Form\Form;
use \Symfony\Component\EventDispatcher\Event;
use \Trinity\Opt\View\Grid as View_Grid;
use \Trinity\Opt\View\Form as View_Form;
use \Trinity\Opt\View\Question as View_Question;
use \Trinity\Web\Controller\Exception as Controller_Exception;
use \Trinity\Web\Http\Redirect;
use \Trinity\Web\Http\Error;
use \Trinity\Web\Controller\Manager;
use \Trinity\WebUtils\Controller\Group\ActionGroup as WebUtils_Controller_Group_ActionGroup;
use \Trinity\WebUtils\Model\Interfaces\Grid as Interface_Grid;

/**
 * The universal CRUD manager.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
abstract class Crud extends WebUtils_Controller_Group_ActionGroup
{
	/**
	 * Here we can define custom templates.
	 * @var array
	 */
	public $templates = array(
		'index' => null,
		'details' => null,
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
	 * @return \Opf\Form\Form
	 */
	abstract public function getForm(Manager $manager, $type);

	/**
	 * Tests the CRUD contract and returns the model used by this controller
	 * group.
	 *
	 * @throws \Trinity\Web\Controller\MException
	 * @param \Trinity\Web\Controller\Manager $manager The controller manager
	 * @param string $actionInterface The action interface name.
	 * @return object
	 */
	protected function _getCrud(Manager $manager, $actionInterface = null)
	{
		$model = $this->initCrud($manager);

		if(!$model instanceof Interface_Grid)
		{
			$manager->events->notify(new Event($this, 'controller.group.crud.error', array(
				'message' => 'Invalid controller model interface: unsupported contract \Trinity\WebUtils\Model\Interfaces\Grid'
			)));

			throw new Controller_Exception('The returned model does not implement Grid interface.');
		}
		if($actionInterface !== null && !is_a($model, $actionInterface))
		{
			$manager->events->notify(new Event($this, 'controller.group.crud.error', array(
				'message' => 'Invalid controller model interface: unsupported contract '.$actionInterface
			)));

			throw new Controller_Exception('Invalid controller model interface: unsupported contract '.$actionInterface);
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
		$view = $manager->getView('Trinity.Opt.View.Grid');

		if($this->templates['index'] !== null)
		{
			$view->setTemplateName('default', $this->templates['index']);
		}
		$translation = $manager->services->get('Translation');

		$view->addModel('grid', $model);
		$view->set('title', $translation->_($model->myName(), 'crud.title'));
		$view->set('page', $manager->request->getParam('page', 1));
		return $view;
	} // end indexAction();

	/**
	 * This action processes the row adding.
	 *
	 * @throws \Trinity\Web\Http\Redirect
	 * @param \Trinity\Web\Controller\Manager $manager The controller manager
	 * @return View
	 */
	public function addAction(Manager $manager)
	{
		$model = $this->_getCrud($manager, '\\Trinity\\WebUtils\\Model\\Interfaces\\Addable');
		$form = $this->getForm($manager, 'add');
		$translation = $manager->services->get('Translation');
		$form->setName('add');

		if($form->execute() == Form::ACCEPTED)
		{
			
			$router = $manager->services->get('Router');
			$flashHelper = $manager->services->get('FlashHelper');
			try
			{
				$model->addItem($form->getValue());
				$flashHelper->addMessage($translation->_($model->myName(), 'crud.message.added'));
			}
			catch(\Trinity\WebUtils\Model\Report $report)
			{
				$flashHelper->addMessage($model->getMessage($report->getMessage()), 'error');
			}
			throw new Redirect($router->assemble(array('action' => 'index')));
		}
		else
		{
			$view = $manager->getView('Trinity.Opt.View.Form');
			if($this->templates['add'] !== null)
			{
				$view->setTemplateName('default', $this->templates['add']);
			}
			$view->set('title', $translation->_($model->myName(), 'crud.add'));
			$view->addModel('form', $form);
			return $view;
		}
	} // end addAction();

	/**
	 * Displays the details of a selected item.
	 *
	 * @param \Trinity\Web\Controller\Manager $manager The controller manager
	 * @return View
	 */
	public function detailsAction(Manager $manager)
	{
		$model = $this->_getCrud($manager, '\\Trinity\\WebUtils\\Model\\Interfaces\\Details');
		try
		{
			$model->id = $manager->request->getParam('id');
			$view = $manager->getView('Trinity.Opt.View.Details');
			if($this->templates['details'] !== null)
			{
				$view->setTemplateName('default', $this->templates['details']);
			}
			$view->addModel('details', $model);

			return $view;
		}
		catch(\Trinity\WebUtils\Model\Report $report)
		{
			$flashHelper = $manager->services->get('FlashHelper');
			$flashHelper->addMessage($report->getMessage(), 'error');
			throw new Redirect($manager->router->assemble(array('action' => 'index')));
		}
	} // end detailsAction();

	/**
	 * This action processes the row editing.
	 *
	 * @throws \Trinity\Web\Http\Redirect
	 * @param \Trinity\Web\Controller\Manager $manager The controller manager
	 * @return View
	 */
	public function editAction(Manager $manager)
	{
		$model = $this->_getCrud($manager, '\\Trinity\\WebUtils\\Model\\Interfaces\\Editable');
		try
		{
			$model->id = $manager->request->getParam('id');
			$form = $this->getForm($manager, 'edit');
			$form->setName('edit');
			$translation = $manager->services->get('Translation');

			if($form->execute() == Form::ACCEPTED)
			{
				$model->editItem($form->getValue());			

				$flashHelper = $manager->services->get('FlashHelper');
				$flashHelper->addMessage($translation->_($model->myName(), 'crud.message.edited'));
				throw new Redirect($manager->router->assemble(array('action' => 'index')));
			}
			else
			{
				$view = $manager->getView('Trinity.Opt.View.Form');
				if($this->templates['edit'] !== null)
				{
					$view->setTemplateName('default', $this->templates['edit']);
				}
				$view->set('title', $translation->_($model->myName(), 'crud.edit'));

				$form->populate($model->getItemForEdit());

				$view->addModel('form', $form);
				return $view;
			}
		}
		catch(\Trinity\WebUtils\Model\Report $report)
		{
			$flashHelper = $manager->services->get('FlashHelper');
			$flashHelper->addMessage($report->getMessage(), 'error');
			throw new Redirect($manager->router->assemble(array('action' => 'index')));
		}
	} // end editAction();

	/**
	 * This action processes the row removal.
	 *
	 * @throws \Trinity\Web\Http\Redirect
	 * @param \Trinity\Web\Controller\Manager $manager The controller manager
	 * @return View
	 */
	public function removeAction(Manager $manager)
	{
		$model = $this->_getCrud($manager, '\\Trinity\\WebUtils\\Model\\Interfaces\\Removable');
		$model->id = $manager->request->getParam('id');
		$answer = $manager->request->getParam('answer');
		$translation = $manager->services->get('Translation');
		switch((string)$answer)
		{
			case 'yes':
				$flashHelper = $manager->services->get('FlashHelper');
				$router = $manager->services->get('Router');
				try
				{
					$model->removeItem();
					$flashHelper->addMessage($translation->_($model->myName(), 'crud.message.removed'));
				}
				catch(\Trinity\WebUtils\Model\Report $report)
				{
					$flashHelper->addMessage($report->getMessage(), 'error');
				}
				throw new Redirect($router->assemble(array('action' => 'index', 'id' => null)));
				break;
			case 'no':
				$router = $manager->services->get('Router');
				throw new Redirect($router->assemble(array('action' => 'index', 'id' => null)));
			default:
				$view = $manager->getView('Trinity.Opt.View.Question');
				$view->set('title', $translation->_($model->myName(), 'crud.remove'));
				$view->addModel('item', $model);
				return $view;
		}
	} // end removeAction();
} // end Crud;