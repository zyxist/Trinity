<?php

namespace Trinity\Hall;
use Trinity\Basement\Hook as Hook;

class Hook_Area implements Hook
{
	public function getDependencies(Bootstrap $application)
	{
		return array(0 =>
			'hall.Module',
		);
	} // end getDependencies();

	/**
	 * Bootstrap this hook.
	 * 
	 * @param Bootstrap $application
	 */
	public function bootstrap(Bootstrap $application)
	{
		
	} // end bootstrap();
} // end Hook_Area;