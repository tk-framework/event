<?php
/**
 * @author Timon Kreis <mail@timonkreis.de>
 * @copyright 2020 Timon Kreis
 * @license http://www.opensource.org/licenses/mit-license.html
 */
declare(strict_types = 1);

use TimonKreis\Framework;

/**
 * @category tk-framework
 * @package event
 */
class DispatcherCest
{
	/**
	 * @param Framework\Test\UnitTester $I
	 */
	public function testGettingListeners(Framework\Test\UnitTester $I) : void
	{
		$dispatcher = new Framework\Event\Dispatcher();

		$I->assertEquals([], $dispatcher->getListeners());
		$I->assertEquals([], $dispatcher->getListeners('event'));
		$I->assertEquals([], $dispatcher->getListeners('event, 50'));
	}

	/**
	 * @param Framework\Test\UnitTester $I
	 */
	public function testSettingListener(Framework\Test\UnitTester $I) : void
	{
		$dispatcher = new Framework\Event\Dispatcher();

		$dispatcher->addListener('event', 'listener', 50);

		$I->assertEquals(['listener'], $dispatcher->getListeners('event', 50));
		$I->assertEquals([50 => ['listener']], $dispatcher->getListeners('event'));
		$I->assertEquals(['event' => [50 => ['listener']]], $dispatcher->getListeners());
	}

	/**
	 * @param Framework\Test\UnitTester $I
	 */
	public function testRemovingListener(Framework\Test\UnitTester $I) : void
	{
		$dispatcher = new Framework\Event\Dispatcher();

		$dispatcher->addListener('event', 'listener', 50);
		$dispatcher->removeListener('event', 'listener', 50);

		$I->assertEquals([], $dispatcher->getListeners());
	}

	/**
	 * @param Framework\Test\UnitTester $I
	 */
	public function testResettingListener(Framework\Test\UnitTester $I) : void
	{
		$dispatcher = new Framework\Event\Dispatcher();

		$dispatcher->addListener('event', 'listener', 50);
		$dispatcher->resetListeners();

		$I->assertEquals([], $dispatcher->getListeners());
	}

	/**
	 * @param Framework\Test\UnitTester $I
	 */
	public function testSortingByPriority(Framework\Test\UnitTester $I) : void
	{
		$dispatcher = new Framework\Event\Dispatcher();
		$dispatcher->addListener('event', 'listener1', 100);
		$dispatcher->addListener('event', 'listener2', 50);

		$listeners = $dispatcher->getListeners('event');
		$listeners = array_keys($listeners);

		$I->assertEquals(50, $listeners[0]);
		$I->assertEquals(100, $listeners[1]);
	}

	/**
	 * @param Framework\Test\UnitTester $I
	 */
	public function testListenerEvent(Framework\Test\UnitTester $I) : void
	{
		$id = uniqid();
		$priority = mt_rand(0, 100);

		$dispatcher = new Framework\Event\Dispatcher();
		$dispatcher->addListener($id, Framework\Event\Tests\EventTestListener::class, $priority);

		$dispatcher->dispatch($id);

		$I->assertEquals($id, $GLOBALS['eventTest']['id']);
		$I->assertEquals($priority, $GLOBALS['eventTest']['priority']);

		unset($GLOBALS['eventTest']);
	}

	/**
	 * @param Framework\Test\UnitTester $I
	 */
	public function testNormalPropagationWithUniquePriorities(Framework\Test\UnitTester $I) : void
	{
		$GLOBALS['propagationCounter'] = 0;

		$dispatcher = new Framework\Event\Dispatcher();
		$dispatcher->addListener('event', Framework\Event\Tests\NormalPropagationListener::class); // +1
		$dispatcher->addListener('event', Framework\Event\Tests\NormalPropagationListener::class); // +1
		$dispatcher->addListener('event', Framework\Event\Tests\NormalPropagationListener::class); // +1
		$dispatcher->addListener('event', Framework\Event\Tests\NormalPropagationListener::class); // +1
		$dispatcher->addListener('event', Framework\Event\Tests\NormalPropagationListener::class); // +1

		$dispatcher->dispatch('event');

		$I->assertEquals(5, $GLOBALS['propagationCounter']);

		unset($GLOBALS['propagationCounter']);
	}

	/**
	 * @param Framework\Test\UnitTester $I
	 */
	public function testNormalPropagationWithDifferentPriorities(Framework\Test\UnitTester $I) : void
	{
		$GLOBALS['propagationCounter'] = 0;

		$dispatcher = new Framework\Event\Dispatcher();
		$dispatcher->addListener('event', Framework\Event\Tests\NormalPropagationListener::class, 1); // +1
		$dispatcher->addListener('event', Framework\Event\Tests\NormalPropagationListener::class, 2); // +1
		$dispatcher->addListener('event', Framework\Event\Tests\NormalPropagationListener::class, 3); // +1
		$dispatcher->addListener('event', Framework\Event\Tests\NormalPropagationListener::class, 4); // +1
		$dispatcher->addListener('event', Framework\Event\Tests\NormalPropagationListener::class, 5); // +1

		$dispatcher->dispatch('event');

		$I->assertEquals(5, $GLOBALS['propagationCounter']);

		unset($GLOBALS['propagationCounter']);
	}

	/**
	 * @param Framework\Test\UnitTester $I
	 */
	public function testStoppedPropagation(Framework\Test\UnitTester $I) : void
	{
		$GLOBALS['propagationCounter'] = 0;

		$dispatcher = new Framework\Event\Dispatcher();
		$dispatcher->addListener('event', Framework\Event\Tests\NormalPropagationListener::class, 1); // +1
		$dispatcher->addListener('event', Framework\Event\Tests\NormalPropagationListener::class, 2); // +1
		$dispatcher->addListener('event', Framework\Event\Tests\StopPropagationListener::class, 3); // stopping
		$dispatcher->addListener('event', Framework\Event\Tests\NormalPropagationListener::class, 4); // skipped
		$dispatcher->addListener('event', Framework\Event\Tests\NormalPropagationListener::class, 5); // skipped
		$dispatcher->addListener('event', Framework\Event\Tests\NormalPropagationListener::class, 6); // skipped

		$dispatcher->dispatch('event');

		$I->assertEquals(2, $GLOBALS['propagationCounter']);

		unset($GLOBALS['propagationCounter']);
	}

	/**
	 * @param Framework\Test\UnitTester $I
	 */
	public function testPausedPropagation(Framework\Test\UnitTester $I) : void
	{
		$GLOBALS['propagationCounter'] = 0;

		$dispatcher = new Framework\Event\Dispatcher();
		$dispatcher->addListener('event', Framework\Event\Tests\NormalPropagationListener::class, 1); // +1
		$dispatcher->addListener('event', Framework\Event\Tests\NormalPropagationListener::class, 2); // +1
		$dispatcher->addListener('event', Framework\Event\Tests\PausePropagationListener::class, 3); // pausing
		$dispatcher->addListener('event', Framework\Event\Tests\NormalPropagationListener::class, 4); // paused
		$dispatcher->addListener('event', Framework\Event\Tests\NormalPropagationListener::class, 5); // paused
		$dispatcher->addListener('event', Framework\Event\Tests\NormalPropagationListener::class, 6); // +1

		$dispatcher->dispatch('event');

		$I->assertEquals(3, $GLOBALS['propagationCounter']);

		unset($GLOBALS['propagationCounter']);
	}

	/**
	 * @param Framework\Test\UnitTester $I
	 */
	public function testParameterManipulation(Framework\Test\UnitTester $I) : void
	{
		$arguments = [
			'value1' => 'before',
			'value2' => 'before'
		];

		$dispatcher = new Framework\Event\Dispatcher();
		$dispatcher->addListener('event', Framework\Event\Tests\ArgumentManipulationListener::class);

		$dispatcher->dispatch('event', $arguments);

		$I->assertEquals('after', $arguments['value1']);
		$I->assertEquals('before', $arguments['value2']);
	}
}
