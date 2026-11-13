<?php

namespace KanboardTests\units;

use Psr\EventDispatcher\StoppableEventInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * Event dispatcher that records called listeners for test assertions
 */
class RecordingEventDispatcher extends EventDispatcher
{
    private array $calledListeners = [];

    public function getCalledListeners(): array
    {
        return $this->calledListeners;
    }

    protected function callListeners(iterable $listeners, string $eventName, object $event)
    {
        $stoppable = $event instanceof StoppableEventInterface;

        foreach ($listeners as $listener) {
            if ($stoppable && $event->isPropagationStopped()) {
                break;
            }

            $this->calledListeners[] = ['event' => $eventName];
            $listener($event, $eventName, $this);
        }
    }
}
