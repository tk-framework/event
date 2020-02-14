<?php
/**
 * @author Timon Kreis <mail@timonkreis.de>
 * @copyright 2020 Timon Kreis
 * @license http://www.opensource.org/licenses/mit-license.html
 */
declare(strict_types = 1);

namespace TimonKreis\Framework\Event\Tests;

use TimonKreis\Framework;

/**
 * @category tk-framework
 * @package event
 */
class PausePropagationListener extends Framework\Event\AbstractListener
{
	/**
	 * Pause further propagation for two steps
	 */
	public function execute() : void
	{
		$this->pausePropagation(2);
	}
}
