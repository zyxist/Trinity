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

/**
 * The default facade executed for every action.
 */
class Standard extends Brick
{
	protected function _dispatch(Controller_Manager $manager)
	{
		// Get style helper
		$styles = $manager->application->getServiceLocator()->get('template.HelperLocator')->get('style');
		// Add local file
		$styles->appendFile('css/layout.css');
		// Add external file
		$styles->appendFile('http://external.style/style.css', false);
		// Add style
		$styles->appendStyle('div.example { color: #ffffff; }');
		// Get script helper
		$scripts = $manager->application->getServiceLocator()->get('template.HelperLocator')->get('script');
		// Add local file
		$scripts->appendFile('js/layout.js');
		// Add external file
		$scripts->appendFile('http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js', false);
		// Add script
		$styles->appendScript('$(\'div.example\').attr(\'id\', \'example\')');
		// Get layout template object.
		$layout = $manager->application->getServiceLocator()->get('template.Layout')->getLayout();
	} // end _dispatch();
} // end Standard;