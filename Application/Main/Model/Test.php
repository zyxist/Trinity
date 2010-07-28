<?php
/**
 * The testing application for Trinity Framework
 *
 * @author Tomasz Jędrzejewski
 */
namespace Application\Main\Model;
use \Trinity\Basement\Model as Model;
use \Trinity\Model\Interfaces\Grid as Interface_Grid;

class Test implements Model
{
	public function getFoo()
	{
		return 'foo';
	} // end getFoo();
} // end Test;