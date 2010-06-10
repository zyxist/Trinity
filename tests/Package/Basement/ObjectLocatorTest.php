<?php
/**
 * The tests for ObjectLocator.
 *
 * @author Tomasz "Zyx" Jędrzejewski
 * @copyright Copyright (c) 2010 Invenzzia Group
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */

/**
 * @covers \Trinity\Basement\EventManager
 */
class Package_Basement_ObjectLocatorTest extends PHPUnit_Framework_TestCase
{
	/**
	 * Initializes the environment for tests.
	 */
	public function setUp()
	{
		\Opl_Registry::set('eventManager', new \Trinity\Basement\EventManager);
	} // end setUp();

	/**
	 * Clears the environment for tests.
	 */
	public function tearDown()
	{
		\Opl_Registry::set('eventManager', null);
	} // end tearDown();

	/**
	 * @covers \Trinity\Basement\ObjectLocator::__construct
	 * @covers \Trinity\Basement\ObjectLocator::getName
	 * @covers \Trinity\Basement\ObjectLocator::getBaseClass
	 * @covers \Trinity\Basement\ObjectLocator::getCreatorFunc
	 */
	public function testObjectLocatorInitializationWithEmptyBaseClass()
	{
		$locator = new \Trinity\Basement\ObjectLocator('foo');

		$this->assertEquals('foo', $locator->getName());
		$this->assertEquals(null, $locator->getBaseClass());
		$this->assertEquals(null, $locator->getCreatorFunc());
	} // end testObjectLocatorInitializationWithEmptyBaseClass();

	/**
	 * @covers \Trinity\Basement\ObjectLocator::__construct
	 * @covers \Trinity\Basement\ObjectLocator::getName
	 * @covers \Trinity\Basement\ObjectLocator::getBaseClass
	 * @covers \Trinity\Basement\ObjectLocator::getCreatorFunc
	 */
	public function testObjectLocatorInitializationWithSetBaseClass()
	{
		$locator = new \Trinity\Basement\ObjectLocator('foo', 'SomeBaseClass');

		$this->assertEquals('foo', $locator->getName());
		$this->assertEquals('SomeBaseClass', $locator->getBaseClass());
		$this->assertEquals(null, $locator->getCreatorFunc());
	} // end testObjectLocatorInitializationWithSetBaseClass();

	/**
	 * @covers \Trinity\Basement\ObjectLocator::__construct
	 * @covers \Trinity\Basement\ObjectLocator::getName
	 * @covers \Trinity\Basement\ObjectLocator::getBaseClass
	 * @covers \Trinity\Basement\ObjectLocator::getCreatorFunc
	 */
	public function testObjectLocatorInitializationWithSetCreator()
	{
		$creator = function($name)
		{
			return new stdClass;
		};

		$locator = new \Trinity\Basement\ObjectLocator('foo', 'SomeBaseClass', $creator);

		$this->assertEquals('foo', $locator->getName());
		$this->assertEquals('SomeBaseClass', $locator->getBaseClass());
		$this->assertSame($creator, $locator->getCreatorFunc());
	} // end testObjectLocatorInitializationWithSetCreator();

	/**
	 * @covers \Trinity\Basement\ObjectLocator::set
	 * @covers \Trinity\Basement\ObjectLocator::get
	 * @covers \Trinity\Basement\ObjectLocator::exists
	 */
	public function testObjectLocatorStoresAnyObjectsWithoutBaseClass()
	{
		$locator = new \Trinity\Basement\ObjectLocator('foo');

		$object1 = new stdClass;
		$object2 = new SplQueue;
		$object3 = new AppendIterator();

		$this->assertFalse($locator->exists('foo'));
		$this->assertFalse($locator->exists('bar'));
		$this->assertFalse($locator->exists('joe'));

		$locator->set('foo', $object1);
		$locator->set('bar', $object2);
		$locator->set('joe', $object3);

		$this->assertTrue($locator->exists('foo'));
		$this->assertTrue($locator->exists('bar'));
		$this->assertTrue($locator->exists('joe'));

		$this->assertSame($object1, $locator->get('foo'));
		$this->assertSame($object2, $locator->get('bar'));
		$this->assertSame($object3, $locator->get('joe'));
	} // end testObjectLocatorStoresAnyObjectsWithoutBaseClass();

	/**
	 * @covers \Trinity\Basement\ObjectLocator::set
	 * @covers \Trinity\Basement\ObjectLocator::get
	 * @expectedException \Trinity\Basement\Core_Exception
	 */
	public function testObjectLocatorStoresConcreteObjectsWithBaseClass()
	{
		$locator = new \Trinity\Basement\ObjectLocator('foo', 'SplStack');

		$object1 = new \SplStack;
		$object2 = new \SplQueue;

		$locator->set('foo', $object1);
		$this->assertTrue($locator->exists('foo'));
		$locator->set('bar', $object2);
	} // end testObjectLocatorStoresConcreteObjectsWithBaseClass();

	/**
	 * @covers \Trinity\Basement\ObjectLocator::set
	 * @covers \Trinity\Basement\ObjectLocator::get
	 * @expectedException \Trinity\Basement\Core_Exception
	 */
	public function testObjectLocatorStoresConcreteObjectsWithBaseClassAsInterface()
	{
		$locator = new \Trinity\Basement\ObjectLocator('foo', '\Trinity\Basement\Model');

		$object1 = $this->getMock('\Trinity\Basement\Model');
		$object2 = new \SplQueue;

		$locator->set('foo', $object1);
		$this->assertTrue($locator->exists('foo'));
		$locator->set('bar', $object2);
	} // end testObjectLocatorStoresConcreteObjectsWithBaseClassAsInterface();

	/**
	 * @covers \Trinity\Basement\ObjectLocator::get
	 * @expectedException \Trinity\Basement\Core_Exception
	 */
	public function testObjectLocatorGetThrowsExceptionIfKeyMissing()
	{
		$locator = new \Trinity\Basement\ObjectLocator('foo');
		$locator->get('abc');
	} // end testObjectLocatorGetThrowsExceptionIfKeyMissing();

	/**
	 * @covers \Trinity\Basement\ObjectLocator::get
	 */
	public function testObjectLocatorGetCreatesNewObjectsWithCreator()
	{
		$obj = $this;
		$count = 0;
		$locator = new \Trinity\Basement\ObjectLocator('foo', 'SplStack', function($name) use($obj, &$count)
		{
			$obj->assertEquals('idx', $name);
			$count++;
			return new SplStack;
		});
		$locator->set('abc', new SplStack);
		$this->assertEquals('SplStack', get_class($locator->get('abc')));
		$this->assertEquals('SplStack', get_class($locator->get('idx')));
		$this->assertEquals('SplStack', get_class($locator->get('idx')));
		$this->assertEquals(1, $count);
	} // end testObjectLocatorGetCreatesNewObjectsWithCreator();
} // end Package_Basement_ObjectLocatorTest;