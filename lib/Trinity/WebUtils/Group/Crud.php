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
	 * @return \Trinity\Basement\Model
	 */
	abstract public function initCrud();

	/**
	 * Should provide forms.
	 *
	 * @return \Opf_Form
	 */
	abstract public function getForm($type);

	/**
	 * Tests the CRUD contract.
	 * 
	 * @param string $actionInterface The action interface name.
	 */
	protected function _getCrud($actionInterface = null)
	{
		$model = $this->initCrud();

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
	 * @return View
	 */
	public function indexAction()
	{
		$model = $this->_getCrud();
		$view = new View_Grid($this->getApplication());

		if($this->templates['index'] !== null)
		{
			$view->setTemplate($this->templates['index']);
		}

		$view->addModel('grid', $model);
		$view->set('title', $model->getMessage('crud.title'));
		return $view;
	} // end indexAction();

	/**
	 * This action processes the row adding.
	 * @return View
	 */
	public function addAction()
	{
		$model = $this->_getCrud('\\Trinity\\Model\\Interfaces\\Addable');
		$form = $this->getForm('add');
		$form->setName('add');

		if($form->execute() == \Opf_Form::ACCEPTED)
		{
			$router = $this->getService('web.Router');
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
			catch(\Exception $exception)
			{
				var_dump(get_class($exception));
			}
			throw new Redirect_Flash($router->assemble(array('action' => 'index'), null, true),
				$flashMessage
			);
		}
		else
		{
			$view = new View_Form($this->getApplication());
			if($this->templates['add'] !== null)
			{
				$view->setTemplate($this->templates['add']);
			}
			$view->set('title', $model->getMessage('crud.add'));
			$view->addModel('form', $form);
			return $view;
		}
	} // end addAction();

	/**
	 * This action processes the row editing.
	 * @return View
	 */
	public function editAction()
	{
		$model = $this->_getCrud('\\Trinity\\Model\\Interfaces\\Editable');
		try
		{
			$model->id = $this->getRequest()->getParam('id');
			$form = $this->getForm('edit');
			$form->setName('edit');

			if($form->execute() == \Opf_Form::ACCEPTED)
			{
				$router = $this->getService('web.Router');
				$model->editItem($form->getValue());

				throw new Redirect_Flash($router->assemble(array('action' => 'index'), null, true),
					$model->getMessage('crud.message.edited')
				);
			}
			else
			{
				$view = new View_Form($this->getApplication());
				if($this->templates['add'] !== null)
				{
					$view->setTemplate($this->templates['edit']);
				}
				$view->set('title', $model->getMessage('crud.edit'));

				$form->populate($model->getItemForEdit());

				$view->addModel('form', $form);
				return $view;
			}
		}
		catch(\Trinity\Model\Report $report)
		{
			$router = $this->getService('web.Router');
			throw new Redirect_Flash($router->assemble(array('action' => 'index'), null, true),
				$report->getMessage(),
				'error'
			);
		}
	} // end editAction();

	/**
	 * This action processes the row removal.
	 * @return View
	 */
	public function removeAction()
	{
		$model = $this->_getCrud('\\Trinity\\Model\\Interfaces\\Removable');
		$model->id = $this->getRequest()->getParam('id');
		$answer = $this->getRequest()->getParam('answer');
		$router = $this->getService('web.Router');
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
				throw new Redirect_Flash($router->assemble(array('action' => 'index'), null, true),
					$flashMessage
				);
				break;
			case 'no':
				throw new Redirect_Exception($router->assemble(array('action' => 'index'), null, true));
			default:
				$view = new View_Question($this->getApplication());
				$view->set('title', $model->getMessage('crud.remove'));
				$view->addModel('item', $model);
				return $view;
				break;
		}
	} // end removeAction();
} // end Crud;