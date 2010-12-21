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

namespace Trinity\Opt\View\Facade;
use \Opt_View;
use \Trinity\Opt\View\Html as View_Html;

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
		$layoutTpl = $this->_serviceLocator->get('Layout')->getLayout();
		$layoutTpl->setFormat('auth', 'Array');

		$auth = $this->getModel('auth', 'Ops\\Auth');

		if(!$auth->hasIdentity())
		{
			Opt_View::assignGlobal('auth', array('identity' => false, 'credentials' => null));
			Opt_View::setFormatGlobal('auth', 'Global/Array', false);
		}
		else
		{
			Opt_View::assignGlobal('auth', array('identity' => true, 'credentials' => $identity = $auth->getIdentity()));

			if(is_object($identity))
			{
				Opt_View::setFormatGlobal('auth', 'Global/Array', false);
				Opt_View::setFormatGlobal('auth.credentials', 'Objective', false);
				Opt_View::setFormatGlobal('auth.credentials.user', 'Objective', false);
			}
			else
			{
				Opt_View::setFormatGlobal('auth', 'Global/Array', false);
				Opt_View::setFormatGlobal('auth.credentials', 'Array', false);
				Opt_View::setFormatGlobal('auth.credentials.user', 'Array', false);
			}
		}
	} // end dispatch();
} // end Login;