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

		$router = $services->get('web.Router');
		$router->keepRoutedVariables(array('module', 'group', 'action', 'id'));

		// Configure helpers.
		$config = $services->get('utils.Config');
		Helper_Url::setBaseUrl($config->baseUrl);
		Helper_Styles::setBaseUrl($config->baseUrl);
		Helper_Styles::setCacheDirectory($config->helpers->styles->cacheDirectory);
		Helper_Styles::setMinify($config->helpers->styles->minify);
		Helper_Styles::set('gzip_contents', $config->helpers->styles->gzip);
		Helper_Javascripts::setBaseUrl($config->baseUrl);
		Helper_Javascripts::setCacheDirectory($config->helpers->javascripts->cacheDirectory);
		Helper_Javascripts::setMinify($config->helpers->javascripts->minify);
		Helper_Javascripts::set('gzip_contents', $config->helpers->javascripts->gzip);
	} // end onInit();
} // end Module;