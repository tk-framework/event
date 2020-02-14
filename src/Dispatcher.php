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
class Dispatcher
{
	public const DEFAULT_PRIORITY = 100;

	/**
	 * @var array
	 */
	protected $listeners = [];

	/**
	 * @var bool
	 */
	protected $listenersDisordered = false;

	/**
	 * Dispatch an event
	 *
	 * @param string $eventId
	 * @param array $arguments
	 * @return $this
	 */
	public function dispatch(string $eventId, array &$arguments = []) : Dispatcher
	{
		$dispatcherState = [
			'stopPropagation' => false,
			'pausePropagation' => 0
		];

		foreach ($this->getListeners($eventId) as $priority => $listenerClasses) {
			$event = new Event($eventId, $priority);

			foreach ($listenerClasses as $listenerClass) {
				// Skip steps if propagation is paused
				if ($dispatcherState['pausePropagation']) {
					--$dispatcherState['pausePropagation'];

					continue;
				}

				/** @var AbstractListener $listener */
				$listener = new $listenerClass($event, $dispatcherState, $arguments);
				$listener->execute();

				// Immediately stop event dispatching
				if ($dispatcherState['stopPropagation']) {
					break 2;
				}
			}
		}

		return $this;
	}

	/**
	 * Get listeners by criteria
	 *
	 * @param string|null $eventId
	 * @param int|null $priority
	 * @return array
	 */
	public function getListeners(string $eventId = null, int $priority = null) : array
	{
		$this->reorder();

		if ($eventId === null) {
			return $this->listeners;
		}

		if ($priority === null) {
			return $this->listeners[$eventId] ?? [];
		}

		return $this->listeners[$eventId][$priority] ?? [];
	}

	/**
	 * Add a new listener
	 *
	 * @param string $eventId
	 * @param string $listenerClass
	 * @param int $priority
	 * @return $this
	 */
	public function addListener(
		string $eventId,
		string $listenerClass,
		int $priority = self::DEFAULT_PRIORITY
	) : Dispatcher {
		if (!isset($this->listeners[$eventId][$priority])) {
			if (!isset($this->listeners[$eventId])) {
				$this->listeners[$eventId] = [];
			}

			$this->listeners[$eventId][$priority] = [];
		}

		$this->listeners[$eventId][$priority][] = $listenerClass;
		$this->listenersDisordered = true;

		return $this;
	}

	/**
	 * Remove listener by criteria
	 *
	 * @param string $eventId
	 * @param string $listenerClass
	 * @param int $priority
	 * @return $this
	 */
	public function removeListener(string $eventId, string $listenerClass, int $priority = 100) : Dispatcher
	{
		if (
			isset($this->listeners[$eventId][$priority])
			&& ($key = array_search($listenerClass, $this->listeners[$eventId][$priority])) !== false
		) {
			unset($this->listeners[$eventId][$priority][$key]);

			// Remove empty priorities
			if (!$this->listeners[$eventId][$priority]) {
				unset($this->listeners[$eventId][$priority]);
			}

			// Remove empty events
			if (!$this->listeners[$eventId]) {
				unset($this->listeners[$eventId]);
			}
		}

		if (!$this->listeners) {
			$this->resetListeners();
		}

		return $this;
	}

	/**
	 * Reset all listeners
	 *
	 * @return $this
	 */
	public function resetListeners() : Dispatcher
	{
		$this->listeners = [];
		$this->listenersDisordered = false;

		return $this;
	}

	/**
	 * Reorder disordered listeners
	 */
	protected function reorder() : void
	{
		if ($this->listenersDisordered) {
			// Sort event names alphabetical order
			ksort($this->listeners, SORT_STRING);

			foreach ($this->listeners as $eventId => $listenerClasses) {
				// Sort priorities in ascending order
				ksort($this->listeners[$eventId], SORT_NUMERIC);
			}

			$this->listenersDisordered = false;
		}
	}
}
