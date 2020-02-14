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
class EventCest
{
	/**
	 * @param Framework\Test\UnitTester $I
	 */
	public function testId(Framework\Test\UnitTester $I) : void
	{
		$id = uniqid();
		$priority = mt_rand(0, 100);
		$event = new Framework\Event\Event($id, $priority);

		$I->assertEquals($id, $event->getId());
	}

	/**
	 * @param Framework\Test\UnitTester $I
	 */
	public function testPriority(Framework\Test\UnitTester $I) : void
	{
		$id = uniqid();
		$priority = mt_rand(0, 100);
		$event = new Framework\Event\Event($id, $priority);

		$I->assertEquals($priority, $event->getPriority());
	}
}
