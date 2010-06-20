<?php
/**
 * The testing application for Trinity Framework
 *
 * @author Tomasz Jędrzejewski
 */
use Trinity\WebUtils\View\ActionGroup as ActionGroupView;

class IndexView extends ActionGroupView
{
	public function indexAction()
	{
		$model = $this->getModel('date', '\\Application\\Main\\Model\\CurrentDate');
		$counter = $this->getModel('session');
		$template = $this->getTemplateObject();
		$template->date = $model->getDate();
		$template->counter = $counter->counter;
		$template->counter2 = $counter->counter2;
	} // end indexAction();
} // end IndexView;