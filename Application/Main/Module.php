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
use \Trinity\Template\Helper_Url;
use \Trinity\Template\Helper_Styles;
use \Trinity\Template\Helper_Javascripts;

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
		$services = $application->getServiceLocator();

		$dm = $services->get('model.Doctrine_Manager');
		$dm->addEntityRepository('MainEntities', $this->getCodePath('Model'));

		$facades = $services->get('web.Facade');
		$facades->addFacade('default', 'Application.Main.Facade.Standard');
		$facades->select('default');
	} // end onInit();
} // end Module;