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
use Trinity\Basement\Application as BaseApplication;

/**
 * The layout manager for Open Power Template.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
class Layout implements View_Broker
{
	/**
	 * The placeholder list with the views.
	 * @var array
	 */
	private $_placeholders = array();

	/**
	 * Layout services enabled?
	 * @var Opt_View
	 */
	private $_layout = null;

	/**
	 * OPT Output System
	 * @var Opt_Output_Interface
	 */
	private $_output = null;

	/**
	 * Layout name.
	 * @var string
	 */
	private $_layoutName = 'layout';

	/**
	 * The application link.
	 * @var \Trinity\Basement\Application
	 */
	private $_application;

	/**
	 * Constructs an empty layout object.
	 * 
	 * @param BaseApplication $application The application.
	 */
	public function __construct(BaseApplication $application)
	{
		$this->_application = $application;
	} // end __construct();

	/**
	 * Disables the layout services. The programmer must render the views
	 * manually. Implements fluent interface.
	 *
	 * @return Trinity\Web\Layout Fluent interface.
	 */
	public function disableLayout()
	{
		$this->_layout = null;

		return $this;
	} // end disableLayout();

	/**
	 * Enables the layout services. Implements
	 * fluent interface.
	 *
	 * @return Trinity\Web\Layout Fluent interface.
	 */
	public function enableLayout()
	{
		$opt = \Opl_Registry::get('opt');
		self::$_mvc->_layout = new \Opt_View($this->_layoutName);

		return $this;
	} // end enableLayout();

	/**
	 * Returns the main layout view object.
	 *
	 * @return Opt_View
	 */
	public function getLayout()
	{
		return $this->_layout;
	} // end getLayoutView();

	/**
	 * Initializes the layout.
	 *
	 * @param string $layout Layout name
	 * @return Opt_View
	 */
	public function setLayout($name)
	{
		$this->_layoutName = $name;

		if($this->_layout !== null)
		{
			$this->_layout->setTemplate($name.'.tpl');
		}
		else
		{
			$this->_layout = new \Opt_View($name.'.tpl');
		}
	} // end setLayout();

	/**
	 * Returns action view list for the specified placeholder.
	 *
	 * @param string $placeholder optional The placeholder name ("content" by default).
	 * @return array
	 */
	public function getViews($placeholder = 'content')
	{
		if(!isset($this->_placeholders[$placeholder]))
		{
			return array();
		}
		return $this->_placeholders[$placeholder];
	} // end getViews();

	/**
	 * Appends a new view to the placeholder.
	 *
	 * @param \Opt_View $view View object.
	 * @param string $placeholder optional Placeholder name.
	 * @return Trinity\Web\Layout
	 */
	public function appendView(\Opt_View $view, $placeholder = 'content')
	{
		if(!isset($this->_placeholders[$placeholder]))
		{
			$this->_placeholders[$placeholder] = array();
		}
		$this->_placeholders[$placeholder][] = $view;

		// Save the used placeholder.
		$view->placeholder = $placeholder;

		return $this;
	} // end appendView();

	/**
	 * Prepends a new view to the placeholder.
	 *
	 * @param \Opt_View $view View object.
	 * @param string $placeholder optional Placeholder name.
	 * @return Trinity\Web\Layout
	 */
	public function prependView(\Opt_View $view, $placeholder = 'content')
	{
		if(!isset($this->_placeholders[$placeholder]))
		{
			$this->_placeholders[$placeholder] = array();
		}
		\array_unshift($this->_placeholders[$placeholder], $view);

		// Save the used placeholder.
		$view->placeholder = $placeholder;

		return $this;
	} // end prependView();

	/**
	 * Sets the OPT output system used to render the page.
	 *
	 * @param \Opt_Output_Interface $output New output interface
	 */
	public function setOutput(\Opt_Output_Interface $output)
	{
		$this->_output = $output;
	} // end setOutput();

	/**
	 * Returns the current output system used to render the page.
	 *
	 * @return \Opt_Output_Interface
	 */
	public function getOutput()
	{
		if($this->_output === null)
		{
			$this->_output = new \Opt_Output_Http;
		}

		return $this->_output;
	} // end getOutput();

	public function setRequest(Request_Abstract $request)
	{

	} // end setRequest();

	public function setResponse(Response_Abstract $response)
	{

	} // end setResponse();

	/**
	 * Renders the views and sends the result to the specified output
	 * system.
	 *
	 * @return boolean
	 */
	public function display()
	{
		$serviceLocator = $this->_application->getServiceLocator();
		$eventManager = $this->_application->getEventManager();

		// Finish configuring Open Power Template
		$opt = $serviceLocator->get('web.Opt');

		$eventManager->fire('web.layout.template.configure',
			array('opt' => $opt)
		);

		$opt->setup();

		// Configure the layout view
		$eventManager->fire('web.layout.configure',
			array('layout' => $this->_layout)
		);

		// Add placeholders
		foreach($this->_placeholders as $name => &$placeholder)
		{
			$data = array();
			foreach($placeholder as $view)
			{
				$data[] = array('view' => $view);
			}
			$this->_layout->assign($name, $data);
		}

		// Render everything. Actually, this is redirected to events, so
		// that we can easily change the exact rendering procedure.
		$eventManager->fire('web.layout.render',
			array('layout' => $this->_layout)
		);
	} // end display();
} // end Layout;