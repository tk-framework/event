<?php
/**
 * @author Timon Kreis <mail@timonkreis.de>
 * @copyright 2020 Timon Kreis
 * @license http://www.opensource.org/licenses/mit-license.html
 */
declare(strict_types = 1);

namespace TimonKreis\Framework\Event;

/**
 * @category tk-framework
 * @package event
 */
class Event
{
	/**
	 * @var string
	 */
	protected $id;

	/**
	 * @var int
	 */
	protected $priority;

	/**
	 * @param string $id
	 * @param int $priority
	 */
	public function __construct(string $id, int $priority)
	{
		$this->id = $id;
		$this->priority = $priority;
	}

	/**
	 * Get the id
	 *
	 * @return string
	 */
	public function getId() : string
	{
		return $this->id;
	}

	/**
	 * Get the priority
	 *
	 * @return int
	 */
	public function getPriority() : int
	{
		return $this->priority;
	}
}
