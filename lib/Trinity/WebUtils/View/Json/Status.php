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
namespace Trinity\WebUtils\View\Json;
use \Trinity\WebUtils\View\Json as View_Json;

/**
 * The JSON status sender.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
class Status extends View_Json
{
	const OK = 1;
	const ERROR = 0;
	const INVALID_DATA = -1;

	/**
	 * The JSON status.
	 * @var integer
	 */
	protected $_status;

	/**
	 * Sets the status sent by the view.
	 * 
	 * @param integer $status The new status
	 */
	public function setStatus($status)
	{
		$this->_status = (int)$status;
	} // end setStatus();

	/**
	 * Dispatches the view.
	 */
	public function dispatch()
	{
		$this->setAnswer(array('status' => $this->_status));
	} // end dispatch();
} // end Status;