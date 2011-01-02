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
namespace Trinity\Opt\Brick;
use \Trinity\Web\Brick;
use \Trinity\Web\Controller\Manager;

/**
 * This is the standard HTTP error controller brick activated
 * by the controller core. You can replace it by modifying the
 * 'trinity.web.controller.httpErrorBrick' option that points to
 * the error processing brick.
 *
 * Note that the captured exception is passed in the controller
 * state.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
class HttpError extends Brick
{
	/**
	 * Dispatches the brick.
	 * 
	 * @param Manager $manager The controller manager
	 * @return View
	 */
	protected function _dispatch(Manager $manager)
	{
		if(($state = $this->getState()) === null)
		{
			throw new Controller_Exception('Missing controller state object in the HttpError brick.');
		}

		$errorView = $manager->getView('Trinity.Opt.View.HttpError');
		$errorView->addModel('error', $state->exception);

		return $errorView;
	} // end _dispatch();
} // end HttpError;