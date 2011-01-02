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
use \Trinity\Web\View\Broker as Broker;
use \Trinity\Web\View\Exception;
use \Trinity\Web\Request;
use \Trinity\Web\Response;
use \Trinity\Opt\Output;

/**
 * This is a special view for displaying HTTP errors. It also serves as
 * a view broker for itself, so that it does not require any layout
 * manager in order to work correctly.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
class HttpError extends View implements Broker
{
	/**
	 * The view template object.
	 * @var \Opt_View
	 */
	protected $_templateObject;

	/**
	 * The HTTP response code.
	 * @var integer
	 */
	protected $_responseCode;

	/**
	 * This is the view broker functionality. Sets the request object.
	 *
	 * @param Request $request The request object.
	 */
	public function setRequest(Request $request)
	{

	} // end setRequest();

	/**
	 * This is the view broker functionality. Sets the response object.
	 *
	 * @param Response $response The response object.
	 */
	public function setResponse(Response $response)
	{
		$this->_output = $output = new Output;
		$templateObject = $this->_templateObject;
		$response->setResponseCode($this->_responseCode);
		$response->setBodyGenerator(function() use($output, $templateObject)
		{
			$opt = \Opl_Registry::get('opt');
			$opt->setup();
			$output->render($templateObject);
			$output->sendBody();
		});
	} // end setResponse();

	/**
	 * This is the view broker functionality. Displays the view.
	 */
	public function display()
	{
		$this->_output->render($this->_templateObject);
	} // end display();

	/**
	 * Returns and optionally launches the view broker object that this
	 * view is designed to work with (layout manager).
	 *
	 * @return Layout
	 */
	public function getViewBroker()
	{
		return $this;
	} // end getViewBroker();

	/**
	 * Installs an external view broker.
	 *
	 * @param View_Broker $broker The view broker to install.
	 */
	public function setViewBroker(Broker $broker)
	{
		if(!$broker instanceof HttpError)
		{
			throw new Exception('Cannot process a HTTP error view: invalid broker.');
		}
	} // end setViewBroker();

	public function dispatch()
	{
		$model = $this->getModel('error');

		$opt = $this->_serviceLocator->get('Opt');

		$this->_responseCode = (int)$model->getCode();

		if($model->getCode() == 500)
		{
			$this->_templateObject = new \Opt_View('trinity.templates:internal.tpl');
			$this->_templateObject->error = array(
				'code' => 500,
				'class' => get_class($model),
				'message' => $model->getMessage(),
				'backtrace' => $model->getTraceAsString(),
			);
		}
		else
		{
			$this->_templateObject = new \Opt_View('trinity.templates:error.tpl');
			$this->_templateObject->error = array(
				'code' => $model->getCode(),
				'message' => $model->getMessage(),
				'backtrace' => $model->getTraceAsString(),
			);
		}
	} // end dispatch();
} // end HttpError;
