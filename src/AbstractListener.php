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
abstract class AbstractListener
{
	/**
	 * @var Event
	 */
	private $_event;

	/**
	 * @var array
	 */
	private $_dispatcherState;

	/**
	 * @var array
	 */
	protected $arguments;

	/**
	 * @param Event $event
	 * @param array $dispatcherState
	 * @param array $arguments
	 */
	public function __construct(Event $event, array &$dispatcherState, array &$arguments)
	{
		$this->_event = $event;
		$this->_dispatcherState = &$dispatcherState;
		$this->arguments = &$arguments;
	}

	/**
	 * Stop further event propagation
	 */
	protected function stopPropagation() : void
	{
		$this->_dispatcherState['stopPropagation'] = true;
	}

	/**
	 * Pause further event propagation for a number of listeners
	 *
	 * @param int $steps
	 */
	protected function pausePropagation(int $steps = 1) : void
	{
		$this->_dispatcherState['pausePropagation'] = $steps;
	}

	/**
	 * @return Event
	 */
	protected function getEvent() : Event
	{
		return $this->_event;
	}

	/**
	 *
	 */
	abstract public function execute() : void;
}
