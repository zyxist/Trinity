<?php
/**
 * The testing application for Trinity Framework
 *
 * @author Tomasz Jędrzejewski
 */

namespace Application\Main;
use \Trinity\Basement\Module as TrinityModule;
use \Trinity\Basement\Application as BaseApplication;
use \Trinity\Basement\EventSubscriber as EventSubscriber;

/**
 * The configuration class for "Main" module.
 *
 * @author Tomasz Jędrzejewski
 */
class Module extends TrinityModule
{

	/**
	 * Connect the models to Doctrine.
	 * 
	 * @param BaseApplication $application The application.
	 */
	public function onInit(BaseApplication $application)
	{
		$dm = $application->getServiceLocator()->get('model.Doctrine_Manager');
		$dm->addEntityRepository('MainEntity', $this->getCodePath('Model'));
	} // end onInit();
} // end Module;