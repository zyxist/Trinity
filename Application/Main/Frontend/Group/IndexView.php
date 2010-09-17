<?php
/**
 * The testing application for Trinity Framework
 *
 * @author Tomasz Jędrzejewski
 */
namespace Application\Main\Frontend\Group;
use Trinity\WebUtils\View\ActionGroup;

class IndexView extends ActionGroup
{
	public function indexAction()
	{
		$model = $this->getModel('date', '\\Application\\Main\\Model\\CurrentDate');
		$counter = $this->getModel('session');
		$template = $this->templateFactory();
		$template->date = $model->getDate();
		$template->counter = $counter->counter;
		$template->counter2 = $counter->counter2;

		return $template;
	} // end indexAction();

	public function brickAction()
	{
		return $this->templateFactory();
	} // end brickAction();
} // end IndexView;