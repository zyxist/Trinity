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
namespace Trinity\Web;


/**
 * The base interface for HTML views.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
abstract class View_Html extends View
{
	/**
	 * The layout manager
	 * @var Layout
	 */
	private $_layout;

	/**
	 * The template name.
	 * @var string
	 */
	private $_template;

	/**
	 * OPT view object.
	 * @var \Opt_View
	 */
	private $_viewTpl;

	/**
	 * Sets the template name used by this view.
	 *
	 * @param string $template The new template name.
	 */
	public function setTemplate($template)
	{
		$this->_template = $template;

		if($this->_viewTpl !== null)
		{
			$this->_viewTpl->setTemplate($template);
		}
	} // end setTemplate();

	/**
	 * Returns the current template name.
	 * 
	 * @return string
	 */
	public function getTemplate()
	{
		return $this->_template;
	} // end getTemplate();

	/**
	 * Returns the template object.
	 * 
	 * @return \Opt_View
	 */
	public function getTemplateObject()
	{
		if($this->_viewTpl === null)
		{
			$this->_viewTpl = new \Opt_View($this->_template);
		}
		return $this->_viewTpl;
	} // end getTemplateObject();

	/**
	 * Returns and optionally launches the view broker object that this
	 * view is designed to work with (layout manager).
	 *
	 * @return Layout
	 */
	public function getViewBroker()
	{
		if($this->_layout === null)
		{
			$this->_layout = $this->_application->getServiceLocator()->get('template.Layout');
		}
		return $this->_layout;
	} // end getViewBroker();

	/**
	 * Installs an external view broker.
	 *
	 * @param View_Broker $broker The view broker to install.
	 */
	public function setViewBroker(View_Broker $broker)
	{
		if(!$broker instanceof Layout)
		{
			throw new View_Exception('Cannot process a HTML view: layout engine missing.');
		}
		$this->_layout = $broker;
	} // end getViewBroker();
} // end View;