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
namespace Trinity\Opt\View;
use \Trinity\Web\View;
use \Trinity\Web\View\Broker;
use \Trinity\Web\View\Exception;
use \Trinity\Opt\Layout;
use \Opt_View;


/**
 * The base interface for HTML views.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
abstract class Html extends View
{
	/**
	 * The layout manager
	 * @var Layout
	 */
	private $_layout;

	/**
	 * The template names.
	 * @var string
	 */
	private $_templates = array();

	/**
	 * This method allows the controllers and bricks to advice
	 * the template name to a generic view. Each template type
	 * used by the view has its unique key, which we assign
	 * the template name to.
	 *
	 * Implements fluent interface.
	 *
	 * @param string $key The view template key.
	 * @param string $template The new template name.
	 * @return \Trinity\Web\View\Html
	 */
	public function setTemplateName($key, $template)
	{
		$this->_templates[$key] = $template;
	} // end setTemplateName();

	/**
	 * Returns the template name assigned to the specified
	 * key.
	 *
	 * @param string $key The view template key.
	 * @return string
	 */
	public function getTemplateName($key = 'default', $defaultTemplateName = null)
	{
		if(!isset($this->_templates[$key]))
		{
			return $defaultTemplateName;
		}
		return $this->_templates[$key];
	} // end getTemplateName();

	/**
	 * This is a factory for OPT template objects which
	 * uses the view template key mechanism to give names.
	 *
	 * @param string $key The view template key
	 * @return \Opt_View
	 */
	public function templateFactory($key = 'default')
	{
		if(!isset($this->_templates[$key]))
		{
			throw new Exception('Cannot create a template object for the view key \''.$key.'\': the key does not exist.');
		}
		
		return new Opt_View($this->_templates[$key]);
	} // end templateFactory();

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
			$this->_layout = $this->_serviceLocator->get('Layout');
		}
		return $this->_layout;
	} // end getViewBroker();

	/**
	 * Installs an external view broker.
	 *
	 * @param View_Broker $broker The view broker to install.
	 */
	public function setViewBroker(Broker $broker)
	{
		if(!$broker instanceof Layout)
		{
			throw new Exception('Cannot process a HTML view: layout engine missing.');
		}
		$this->_layout = $broker;
	} // end setViewBroker();
} // end Html;