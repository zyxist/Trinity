<?php
/**
 * The testing application for Trinity Framework
 *
 * @author Tomasz Jędrzejewski
 */

namespace Application\Frontend;
use \Trinity\Web\Area;

class Module extends Area
{
	protected $_metadata = array(
		'controllerService' => 'GroupController',
		'defaultGroup' => 'index',
		'defaultArea' => 'index'
	);

	/**
	 * Returns the area name.
	 *
	 * @return string
	 */
	public function getAreaName()
	{
		return 'frontend';
	} // end getAreaName();

	public function launch()
	{
		parent::launch();
		$serviceLocator = $this->getServiceLocator();
		$facadeManager = $serviceLocator->get('Facade');
		$facadeManager->addFacade('frontend', '\Application\Facade\Standard');
		$facadeManager->select('frontend');
	} // end launch();
} // end Module;