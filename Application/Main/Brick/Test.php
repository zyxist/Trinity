<?php
/**
 * The testing application for Trinity Framework
 *
 * @author Tomasz Jędrzejewski
 */
namespace Application\Main\Brick;
use \Trinity\Web\Brick;
use \Trinity\Web\Controller\Manager;

/**
 * A test brick.
 */
class Test extends Brick
{
	protected function _dispatch(Manager $manager)
	{
		$model = $manager->getModel('Application.Main.Model.Test');
		$view = $manager->getView('Application.Main.Frontend.View.Test');
		$view->addModel('model', $model);
		return $view;
	} // end _dispatch();
} // end Test;