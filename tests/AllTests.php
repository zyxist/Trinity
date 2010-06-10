<?php
/**
 * The test suite file that configures the execution of the test cases.
 *
 * @author Tomasz "Zyx" Jędrzejewski
 * @copyright Copyright (c) 2009 Invenzzia Group
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */

require_once 'PHPUnit/Framework.php';
require_once './Package/AllTests.php';

class AllTests
{
	/**
	 * Configures the suite object.
	 *
	 * @return Suite
	 */
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite('Package');
		$suite->addTest(Package_AllTests::suite());

		return $suite;
	} // end suite();
} // end AllTests;