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

namespace Trinity\WebUtils\View\Facade;
use \Trinity\Web\View\Html as View_Html;
use \Trinity\Web\Controller_Exception as Web_Controller_Exception;

/**
 * One of the default facade views. It passes the currently authenticated
 * user information to the layout view.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
class Login extends View_Html
{
	/**
	 * Dispatches the view.
	 */
	public function dispatch()
	{
		$layoutTpl = $this->_application->getServiceLocator()->get('template.Layout')->getLayout();
		$layoutTpl->setFormat('auth', 'Array');

		$auth = $this->getModel('auth', 'Ops\\Auth');

		if(!$auth->hasIdentity())
		{
			$layoutTpl->auth = array('identity' => false, 'credentials' => null);
			$layoutTpl->setFormat('auth', 'Array');
		}
		else
		{
			$layoutTpl->auth = array('identity' => true, 'credentials' => $identity = $auth->getIdentity());

			if(is_object($identity))
			{
				$layoutTpl->setFormat('auth.identity', 'Objective');
			}
			else
			{
				$layoutTpl->setFormat('auth.identity', 'Array');
			}
		}
	} // end dispatch();
} // end Login;