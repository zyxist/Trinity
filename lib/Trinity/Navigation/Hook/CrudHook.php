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
namespace Trinity\Navigation\Hook;
use \Trinity\Navigation\Hook;
use \Trinity\Navigation\Page;
use \Trinity\WebUtils\Model\Interfaces\Brief;
use \Trinity\WebUtils\Model\Interfaces\Details;
use \Trinity\WebUtils\Model\Interfaces\Editable;
use \Trinity\WebUtils\Model\Interfaces\Removable;
use \Trinity\WebUtils\Model\Report\Unavailable;

class CrudHook implements Hook
{
	/**
	 * The model used by the hook.
	 * @var Brief
	 */
	protected $_model;

	/**
	 * The navigation data
	 * @var array
	 */
	protected $_routingData;

	/**
	 * Creates the navigation hook.
	 *
	 * @param Brief $model The CRUD model.
	 * @param array $routingData The router data pattern
	 */
	public function __construct(Brief $model, array $routingData)
	{
		$this->_model = $model;
		$this->_routingData = $routingData;
	} // end __construct();

	/**
	 * Adds the information about the specified page.
	 *
	 * @param Page $page The page to fill.
	 */
	public function createPageInfo(Page $page)
	{
		try
		{
			$item = $this->_model->getBriefInformation();

			$routingData = $this->_routingData;
			$routingData['id'] = $item['id'];
			$routingData['action'] = 'details';
			$page->routingData = $routingData;

			$page->id = $item['id'];
			$page->title = $item['title'];
		}
		catch(Unavailable $report)
		{
			// null
		}
	} // end createPageInfo();

	/**
	 * Adds the children for the specified page.
	 *
	 * @param Page $page The page to modify.
	 */
	public function createPageChildren(Page $page)
	{
		if($page->id !== null)
		{
			if($this->_model instanceof Editable)
			{
				$page->appendChild($this->_pageFactory('Edit', 'edit', $page->id));
			}
			if($this->_model instanceof Removable)
			{
				$page->appendChild($this->_pageFactory('Remove', 'remove', $page->id));
			}
		}
	} // end createPageChildren();

	/**
	 * Creates the page object with the given title.
	 *
	 * @param string $title The page title
	 * @param string $action The page action
	 * @param int $id The item ID
	 * @return Page
	 */
	protected function _pageFactory($title, $action, $id)
	{
		$page = new Page;
		$page->setPageType(Page::TYPE_REAL);

		$routingData = $this->_routingData;
		$routingData['id'] = $id;
		$routingData['action'] = $action;
		$page->routingData = $routingData;
		$page->controller = 'group';
		$page->title = $title;

		return $page;
	} // end _pageFactory();
} // end AdminHook;