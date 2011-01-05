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
use \Symfony\Component\EventDispatcher\EventDispatcher;
use \Symfony\Component\EventDispatcher\Event;
use \Trinity\Basement\Application as Basement_Application;
use \Trinity\Basement\ServiceLocator;

/**
 * The basic application code for writing web applications.
 *
 * @author Tomasz Jędrzejewski
 * @copyright Invenzzia Group <http://www.invenzzia.org/> and contributors.
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */
abstract class Application extends Basement_Application
{
	/**
	 * The area module, when not using strategies.
	 * @var Area
	 */
	protected $_areaModule = null;

	/**
	 * This method can be used by the entry script to hard-code area selection
	 * without a strategy. 
	 * 
	 * @param Area $area The area object.
	 */
	public function setAreaModule(Area $area)
	{
		$this->_areaModule = $area;
	} // end setAreaModule();

	/**
	 * Returns the area module, when not using strategies.
	 *
	 * @return Area
	 */
	public function getAreaModule()
	{
		return $this->_areaModule;
	} // end getAreaModule();

	/**
	 * Returns and optionally constructs the service locator.
	 *
	 * @return \Trinity\Basement\ServiceLocator
	 */
	public function getServiceLocator()
	{
		if($this->_serviceLocator !== null)
		{
			return $this->_serviceLocator;
		}

		$this->_serviceLocator = new ServiceLocator('service locator');

		$config = $this->_serviceLocator->getConfiguration();
		$config->set('application.directory', $this->getDirectory());

		$this->_serviceLocator->set('Application', $this);
		$this->_serviceLocator->set('EventDispatcher', new EventDispatcher());
		$this->_serviceLocator->registerServiceContainer(new Services);

		return $this->_serviceLocator;
	} // end getServiceLocator();

	/**
	 * Launches the web application.
	 */
	public function launch()
	{
		try
		{
			// Do the ordinary stuff
			parent::launch();

			// Now run the web MVC stack.
			$serviceLocator = $this->getServiceLocator();
			$eventDispatcher = $serviceLocator->get('EventDispatcher');
			$areaManager = $serviceLocator->get('AreaManager');

			// Get the active area
			if(($area = $areaManager->getActiveArea()) === null)
			{
				if($areaManager->getAreaStrategy() !== null)
				{
					$area = $areaManager->discoverActiveArea();
				}
				else
				{
					throw new Exception('No active area is selected.');
				}
			}
			$area->updateMetadata();

			// Get the active module
			$request = $serviceLocator->get('Request');
			$response = $serviceLocator->get('Response');

			$params = $request->getParams();
			if(empty($params))
			{
				if($area->has('defaultRoute'))
				{
					$request->setParams($area->get('defaultRoute'));
				}
				else
				{
					$config = $serviceLocator->getConfiguration();
					$request->setParams(array(
						'module' => $config->get('trinity.web.controller.defaultModule')
					));
				}
			}

			$areaManager->setActiveModule($request->getParam('module'));
			$module = $areaManager->getActiveModule();

			$eventDispatcher->notify(new Event($this, 'web.application.modules-discovered', array('module' => $module, 'area' => $area)));

			// Get the controller
			$controller = $serviceLocator->get($area->controllerService);
			if($controller instanceof Controller)
			{
				$controller->dispatch($request, $response);
			}
			else
			{
				throw new Exception('The selected area controller is not a valid controller instance.');
			}
			$response->sendResponse();
		}
		catch(Exception $exception)
		{
			$this->processException($exception);
		}
	} // end launch();

	/**
	 * Processes all the system errors.
	 * 
	 * @param Exception $exception The exception to process
	 */
	public function processException(Exception $exception)
	{
		$this->panic($exception);
		/*
		try
		{
			
		}
		catch(Exception $exception)
		{
			// Oops, the situation is really critical.
			
		}
		 */
	} // end processException();

	/**
	 * Processes the critical situations.
	 * 
	 * @param Exception $exception
	 */
	public function panic(Exception $exception)
	{
		header('Content-type: text\html;charset=utf-8');
?>
<html>
  <head>
	<title>Internal Trinity error</title>
  </head>
  <body>
	<h3>Internal Trinity error</h3>
	<p><?php echo $exception->getMessage(); ?></p>
	<p><?php echo get_class($exception); ?></p>
	<?php var_dump($exception->getTrace()); ?>
  </body>
</html>
<?php
	} // end panic();
} // end Application;