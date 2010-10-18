<?php
/**
 * The testing application for Trinity Framework
 *
 * @author Tomasz Jędrzejewski
 */
namespace Addon\Frontend\Group;
use \Trinity\Web\Controller\Manager;
use \Trinity\Opt\View\Grid;
use \Trinity\Opt\Controller\Group\ActionGroup;

class IndexGroup extends ActionGroup
{
	public function indexAction()
	{
		return $this->getActionView();
	} // end indexAction();
} // end IndexGroup;