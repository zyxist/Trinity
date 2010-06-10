<?php
/**
 * The tests for EventHandler.
 *
 * @author Tomasz "Zyx" Jędrzejewski
 * @copyright Copyright (c) 2010 Invenzzia Group
 * @license http://www.invenzzia.org/license/new-bsd New BSD License
 */

/**
 * @covers \Trinity\Basement\EventManager
 */
class Package_Basement_EventManagerTest extends PHPUnit_Framework_TestCase
{
	public function testFiringEmptyEvent()
	{
		$eh = new \Trinity\Basement\EventManager;
		$eh->fire('foo');
	} // end testFiringEmptyEvent();

	public function testFiringSingleEvent()
	{
		$fired = 0;
		$eh = new \Trinity\Basement\EventManager;
		$eh->addCallback('foo', function($params) use(&$fired){
			$fired++;
		});
		$eh->fire('foo');

		$this->assertEquals(1, $fired);
	} // end testFiringSingleEvent();

	/**
	 * @cover \Trinity\Basement\EventManager::fire
	 * @cover \Trinity\Basement\EventManager::connect
	 */
	public function testFiringMultipleEvents()
	{
		$fired = 0;
		$eh = new \Trinity\Basement\EventManager;
		$eh->addCallback('foo', $func = function($params) use(&$fired){
			$fired++;
		});
		$eh->addCallback('foo', $func);
		$eh->fire('foo');

		$this->assertEquals(2, $fired);
	} // end testFiringMultipleEvents();

	/**
	 * @cover \Trinity\Basement\EventManager::fire
	 */
	public function testPassingArguments()
	{
		$fired = 0;
		$eh = new \Trinity\Basement\EventManager;
		$eh->addCallback('foo', $func = function($params) use(&$fired){
			if(isset($params['bar']) && $params['bar'] == 'joe')
			{
				$fired++;
			}
		});
		$eh->fire('foo', array('bar' => 'joe'));
		
		$this->assertEquals(1, $fired);
	} // end testPassingArguments();

	/**
	 * @cover \Trinity\Basement\EventManager::fire
	 */
	public function testFiringSingletonCallback()
	{
		$fired = 0;
		$eh = new \Trinity\Basement\EventManager;
		$eh->addCallback('foo', function($params) use(&$fired){
			$fired++;
			return \Trinity\Basement\EventManager::REMOVE;
		});
		$eh->fire('foo');
		$eh->fire('foo');

		$this->assertEquals(1, $fired);
	} // end testFiringSingletonCallback();

	/**
	 * @cover \Trinity\Basement\EventManager::addListener
	 * @cover \Trinity\Basement\EventManager::removeListener
	 * @cover \Trinity\Basement\EventManager::fire
	 */
	public function testAddingEventListener()
	{
		$fired = 0;
		$eh = new \Trinity\Basement\EventManager;

		$listener = $this->getMock('\Trinity\Basement\EventListener', array('dispatchEvent'));
		$listener->expects($this->exactly(3))
			->method('dispatchEvent');

		$eh->addListener(array('foo', 'bar'), $listener);

		$eh->fire('foo');
		$eh->fire('bar');

		$eh->removeListener('foo', $listener);

		$eh->fire('foo');
		$eh->fire('bar');
	} // end testAddingEventListener();

	/**
	 * @cover \Trinity\Basement\EventManager::addSubscriber
	 * @cover \Trinity\Basement\EventManager::removeSubscriber
	 * @cover \Trinity\Basement\EventManager::fire
	 */
	public function testAddingEventSubscriber()
	{
		$fired = 0;
		$eh = new \Trinity\Basement\EventManager;

		$subscriber = $this->getMock('\Trinity\Basement\EventSubscriber', array('dispatchEvent', 'getSubscribedEvents'));
		$subscriber->expects($this->exactly(2))
			->method('dispatchEvent');
		$subscriber->expects($this->exactly(2))
			->method('getSubscribedEvents')
			->will($this->returnValue(array('foo', 'bar')));

		$eh->addSubscriber($subscriber);

		$eh->fire('foo');
		$eh->fire('bar');

		$eh->removeSubscriber($subscriber);

		$eh->fire('foo');
		$eh->fire('bar');
	} // end testAddingEventSubscriber();

	/**
	 * @cover \Trinity\Basement\EventManager::fire
	 */
	public function testMixMethodsForSingleEvent()
	{
		$fired = 0;
		$eh = new \Trinity\Basement\EventManager;

		$listener = $this->getMock('\Trinity\Basement\EventListener', array('dispatchEvent'));
		$listener->expects($this->exactly(1))
			->method('dispatchEvent');

		$eh->addListener('foo', $listener);
		$fired = 0;
		$eh->addCallback('foo', function($params) use(&$fired){
			$fired++;
		});

		$eh->fire('foo');
		$this->assertEquals(1, $fired);
	} // end testMixMethodsForSingleEvent();
} // end Package_Basement_EventManagerTest;