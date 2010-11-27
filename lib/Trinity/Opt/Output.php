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

namespace Trinity\Opt;
use \Opt_Output_Interface;

/**
 * The output interface for Open Power Template that redirects the rendered
 * template to the response object. It should be injected as a body generator
 * to the response.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
class Output implements Opt_Output_Interface
{
	/**
	 * The template mode used by OPT.
	 * @var integer
	 */
	private $_templateMode = null;

	/**
	 * Remember the view to render
	 * @var \Opt_View
	 */
	private $_view;

	/**
	 * Returns the output system name.
	 *
	 * @return string
	 */
	public function getName()
	{
		return 'Trinity_OPT';
	} // end getName();

	/**
	 * Stores the OPT view object for further rendering.
	 *
	 * @param Opt_View $view The view object to render.
	 */
	public function render(\Opt_View $view)
	{
		$this->_view = $view;
	} // end render();

	/**
	 * Sends the body to the browser.
	 *
	 * @return mixed
	 */
	public function sendBody()
	{
		if($this->_templateMode === null)
		{
			$this->_templateMode = $this->_view->getParser();
			ob_start();
		}
		elseif($this->_templateMode == \Opt_Class::XML_MODE)
		{
			throw new \Opt_Output_Exception('Output overloaded - a main XML template has already been rendered.');
		}
		$result = $this->_view->_parse($this, $this->_templateMode);
		echo ob_get_clean();

		return $result;
	} // end render();
} // end Output;
