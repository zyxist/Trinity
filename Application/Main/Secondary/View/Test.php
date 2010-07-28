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
namespace Application\Main\Secondary\View;
use \Trinity\Web\View_Html as View_Html;

class Test extends View_Html
{
	/**
	 * Dispatches the view.
	 */
	public function dispatch()
	{
		$this->setTemplate('area.templates:test.tpl');
		$view = $this->getTemplateObject();

		$layout = $this->_application->getServiceLocator()->get('template.Layout');
		$layout->appendView($view);
	} // end dispatch();
} // end Test;