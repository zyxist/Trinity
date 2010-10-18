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
namespace Application\Secondary\View;
use \Trinity\Opt\View\Html as View_Html;

class Test extends View_Html
{
	/**
	 * Dispatches the view.
	 */
	public function dispatch()
	{
		$view = new \Opt_View('current.templates:test.tpl');

		$layout = $this->_serviceLocator->get('Layout');
		$layout->appendView($view);
	} // end dispatch();
} // end Test;