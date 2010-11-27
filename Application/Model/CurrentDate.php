<?php
/**
 * The testing application for Trinity Framework
 *
 * @author Tomasz Jędrzejewski
 */
namespace Application\Model;

class CurrentDate
{
	public function getDate()
	{
		return date('r');
	} // end getDate();
} // end CurrentDate;