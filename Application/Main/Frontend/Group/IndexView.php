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
		$template = $this->getTemplateObject();
		$template->date = $model->getDate();
	} // end indexAction();
} // end IndexView;