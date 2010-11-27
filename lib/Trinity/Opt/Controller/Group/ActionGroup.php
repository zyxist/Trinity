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
use \Symfony\Component\EventDispatcher\Event;
use \Trinity\Web\Controller\Exception;
use \Trinity\Web\Controller\Manager;
use \Trinity\WebUtils\Controller\Group\ActionGroup as WebUtils_Controller_Group_ActionGroup;
use \Trinity\Opt\View\ActionGroup as Opt_View_ActionGroup;

/**
 * The extension to the action group that adds the support for OPT-based action
 * views.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
class ActionGroup extends WebUtils_Controller_Group_ActionGroup
{
	/**
	 * The default group view
	 * @var \Trinity\WebUtils\View\ActionGroup
	 */
	private $_view = false;

	/**
	 * Returns and optionally constructs the action group view accompanying
	 * the group.
	 *
	 * @param string $name The attribute name.
	 * @return \Trinity\WebUtils\View\ActionGroup
	 */
	public function getActionView()
	{
		if($this->_view === false)
		{
			$reflection = new \ReflectionObject($this);

			$className = $reflection->getNamespaceName().'\\'.$this->_groupName.'View';
			$fileName = dirname($reflection->getFileName()).DIRECTORY_SEPARATOR.$this->_groupName.'View.php';

			if(!file_exists($fileName))
			{
				$this->_view = null;
				return null;
			}

			require($fileName);

			if(!\class_exists($className, false))
			{
				$this->_view = null;
				return null;
			}
			$this->_view = new $className($this->_manager->services);
			if(!$this->_view instanceof Opt_View_ActionGroup)
			{
				throw new Controller_Exception('The loaded view class is not an instance of \Trinity\Opt\View\ActionGroup');
			}
			$this->_view->bind($this->_groupName, $this->_actionName);
		}
		return $this->_view;
	} // end getActionView();
} // end ActionGroup;
