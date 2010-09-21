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

namespace Trinity\Template;
use \Symfony\Component\EventDispatcher\Event;
use Trinity\Basement\Application as BaseApplication;
use Trinity\Web\View\Broker;
use Trinity\Web\Request;
use Trinity\Web\Response;
use Trinity\Template\Exception;
use Opt_View;
use Opt_Output_Interface;

/**
 * The layout manager for Open Power Template.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
class Layout implements Broker
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
	 * @return Trinity\Template\Layout Fluent interface.
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
	 * @return Trinity\Template\Layout Fluent interface.
	 */
	public function enableLayout()
	{
		$this->_layout = new Opt_View($this->_layoutName);

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
			$this->_layout = new Opt_View($name.'.tpl');
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
		$data = array();
		for($i = 0, $endI = count($this->_placeholders[$placeholder]); $i<$endI; $i++)
		{
			$data[] = &$this->_placeholders[$placeholder][$i]['view'];
		}
		return $data;
	} // end getViews();

	/**
	 * Appends a new view to the placeholder.
	 *
	 * @param \Opt_View $view View object.
	 * @param string $placeholder optional Placeholder name.
	 * @return Trinity\Template\Layout Fluent interface.
	 */
	public function appendView(Opt_View $view, $placeholder = 'content')
	{
		if(!isset($this->_placeholders[$placeholder]))
		{
			$this->_placeholders[$placeholder] = array();
		}
		$this->_placeholders[$placeholder][] = array('view' => $view);

		// Save the used placeholder.
		$view->placeholder = $placeholder;

		return $this;
	} // end appendView();

	/**
	 * Prepends a new view to the placeholder.
	 *
	 * @param \Opt_View $view View object.
	 * @param string $placeholder optional Placeholder name.
	 * @return Trinity\Template\Layout
	 */
	public function prependView(Opt_View $view, $placeholder = 'content')
	{
		if(!isset($this->_placeholders[$placeholder]))
		{
			$this->_placeholders[$placeholder] = array();
		}
		\array_unshift($this->_placeholders[$placeholder], array('view' => $view));

		// Save the used placeholder.
		$view->placeholder = $placeholder;

		return $this;
	} // end prependView();

 	/**
	 * Assigns a new view to the placeholder.
     *
	 * @param \Opt_View $view View object.
	 * @param string $placeholder Placeholder name.
	 * @return Trinity\Template\Layout
	 */
	public function assignView(Opt_View $view, $placeholder)
	{
		if(isset($this->_placeholders[$placeholder]))
		{
			throw new Exception('Placeholder "'.$placeholder.'" is already used!');
			return $this;
		}
		$this->_placeholders[$placeholder] = $view;

		return $this;
	} // end assignView();

	/**
	 * Not needed in this particular case.
	 *
	 * @param Request_Abstract $request
	 */
	public function setRequest(Request $request)
	{
		/* null */
	} // end setRequest();

	/**
	 * Configures the response object to capture the output from this
	 * view broker.
	 *
	 * @param Response_Abstract $response The response object.
	 */
	public function setResponse(Response $response)
	{
		$this->_output = $output = new Output;
		$response->setBodyGenerator(function() use($output)
		{
			$output->sendBody();
		});
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
		$eventDispatcher = $this->_application->getEventDispatcher();

		// Finish configuring Open Power Template
		$opt = $serviceLocator->get('template.Opt');

		$eventDispatcher->notify(new Event($this, 'template.layout.template.configure',
			array('opt' => $opt)
		));

		$opt->setup();

		if($this->_layout === null)
		{
			$this->_layout = array_shift($this->_placeholders);
			if(is_array($this->_layout))
			{
				$this->_layout = $this->_layout[0]['view'];
			}
		}
		else
		{
			// Configure the layout view
			$eventDispatcher->notify(new Event($this, 'template.layout.configure',
				array('layout' => $this->_layout)
			));
		}

		// Add placeholders
		foreach($this->_placeholders as $name => &$placeholder)
		{
			$this->_layout->assign($name, $placeholder);
		}

		// Render everything. Actually, this is redirected to events, so
		// that we can easily change the exact rendering procedure.
		$eventDispatcher->notify(new Event($this, 'template.layout.render',
			array('layout' => $this->_layout)
		));
		$this->_output->render($this->_layout);
	} // end display();
} // end Layout;
