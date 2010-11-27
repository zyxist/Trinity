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
use \Trinity\Opt\Form\ManyToMany as SelectionForm;
use \Trinity\Web\Controller\Exception as Controller_Exception;
use \Trinity\Web\Http\Redirect;
use \Trinity\Web\Http\Error;
use \Trinity\Web\Controller\Manager;
use \Trinity\WebUtils\Controller\Group\ActionGroup as WebUtils_Controller_Group_ActionGroup;
use \Trinity\WebUtils\Model\Interfaces\ManyToMany as Interface_ManyToMany;

/**
 * The universal CRUD manager.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
abstract class ManyToMany extends WebUtils_Controller_Group_ActionGroup
{
	/**
	 * Here we can define custom templates.
	 * @var array
	 */
	public $templates = array(
		'index' => null,
	);

	/**
	 * The CRUD initializer. It should return the model used by the CRUD.
	 *
	 * @param \Trinity\Web\Controller\Manager $manager The controller manager
	 * @return \Trinity\Basement\Model
	 */
	abstract public function initManyToMany(Manager $manager);

	/**
	 * Tests the CRUD contract and returns the model used by this controller
	 * group.
	 *
	 * @throws \Trinity\Web\Controller\MException
	 * @param \Trinity\Web\Controller\Manager $manager The controller manager
	 * @param string $actionInterface The action interface name.
	 * @return object
	 */
	protected function _getManyToMany(Manager $manager)
	{
		$model = $this->initManyToMany($manager);

		if(!$model instanceof Interface_ManyToMany)
		{
			$manager->events->notify(new Event($this, 'controller.group.crud.error', array(
				'message' => 'Invalid controller model interface: unsupported contract \Trinity\WebUtils\Model\Interfaces\Grid'
			)));

			throw new Controller_Exception('The returned model does not implement Grid interface.');
		}
		return $model;
	} // end _getManyToMany();

	public function indexAction(Manager $manager)
	{
		$model = $this->_getManyToMany($manager);
		$model->parentId = $manager->request->getParam('parentId');
		$view = $manager->getView('Trinity.Opt.View.ManyToMany');

		if($this->templates['index'] !== null)
		{
			$view->setTemplateName('default', $this->templates['index']);
		}
		$translation = $manager->services->get('Translation');

		$form = new SelectionForm($manager->services->get('Opf'), 'add');
		if($form->execute() == Form::ACCEPTED)
		{
			$data = $form->getValue();
			$model->add($data['id']);
		}
		$view->set('page', $manager->request->getParam('page', 1));
		$view->addModel('list', $model);
		$view->addModel('form', $form);
		return $view;
	} // end indexAction();

	public function removeAction(Manager $manager)
	{
		$model = $this->_getManyToMany($manager);
		$model->parentId = $manager->request->getParam('parentId');
		$manager->router->setParam('parentId', $model->parentId);
		$answer = $manager->request->getParam('answer');
		$translation = $manager->services->get('Translation');
		switch((string)$answer)
		{
			case 'yes':
				$flashHelper = $manager->services->get('FlashHelper');
				$router = $manager->services->get('Router');
				try
				{
					$model->remove($manager->request->getParam('id'));
					$flashHelper->addMessage($translation->_($model->myName(), 'manytomany.message.removed'));
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
				$view->set('title', $translation->_($model->myName(), 'manytomany.remove'));
				$view->addModel('item', $model);
				return $view;
		}
	} // end removeAction();
} // end ManyToMany;