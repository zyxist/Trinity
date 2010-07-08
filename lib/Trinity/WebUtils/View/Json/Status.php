<?php
/**
 * O-note electronic school book.
 *
 * This is a proprietary software. You are not allowed to use, redistribute,
 * modify or sell it without a prior written permission of CleverIT. Please
 * report every abuses to CleverIT <http://www.cleverit.com.pl/>
 *
 * Copyright (c) CleverIT 2010. All rights reserved.
 */
namespace Trinity\WebUtils\View\Json;
use \Trinity\Basement\Application as BaseApplication;
use \Trinity\Web\View\Json as View_Json;

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

	private $_status;

	public function __construct(BaseApplication $application, $status)
	{
		parent::__construct($application);
		$this->_status = (int)$status;
	} // end __construct();

	public function dispatch()
	{
		$this->setAnswer(array('status' => $this->_status));
	} // end dispatch();
} // end LessonReadNotesView;