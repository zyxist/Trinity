<?php
/**
 * The testing application for Trinity Framework
 *
 * @author Tomasz Jędrzejewski
 */
namespace Application\Main\Model;
use \Trinity\Basement\Model as Model;

class CurrentDate implements Model
{
	public function getDate()
	{
		return date('r');
	} // end getDate();
} // end CurrentDate;