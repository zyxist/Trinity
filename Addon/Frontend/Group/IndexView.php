<?php
/**
 * The testing application for Trinity Framework
 *
 * @author Tomasz Jędrzejewski
 */
namespace Addon\Frontend\Group;
use \Trinity\Opt\View\ActionGroup;

class IndexView extends ActionGroup
{
	public function indexAction()
	{
		$template = $this->templateFactory();
		$template->foo = 'Hi universe. I\'m a teapot.';

		return $template;
	} // end indexAction();
} // end IndexView;