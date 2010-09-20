<?php
/**
 * The testing application for Trinity Framework
 *
 * @author Tomasz Jędrzejewski
 */
namespace Application\Main\Facade;
use \Trinity\Web\Brick;
use \Trinity\Web\Controller\Manager as Controller_Manager;
use \Trinity\Web\Facade\Manager as Facade_Manager;
use \Trinity\Template\Helper_Styles;
use \Trinity\Template\Helper_Javascripts;

/**
 * The default facade executed for every action.
 */
class Standard extends Brick
{

	protected function _dispatch(Controller_Manager $manager)
	{
		// Add layout style
		Helper_Styles::append('css/layout.css');
		// Add external style
		Helper_Styles::append('http://external.style/style.css', false);
		// Add layout script
		Helper_Javascripts::append('js/layout.js');
		// Add external script
		Helper_Javascripts::append('http://external.script/script.js', false);
		// Get layout template object.
		$layout = $manager->application->getServiceLocator()->get('template.Layout')->getLayout();
		// Add styles and scripts arrays
		$layout->styles = Helper_Styles::getStyles();
		$layout->scripts = Helper_Javascripts::getScripts();
	} // end _dispatch();
} // end Standard;