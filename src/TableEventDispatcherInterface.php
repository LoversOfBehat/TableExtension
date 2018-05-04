<?php

declare(strict_types = 1);

namespace LoversOfBehat\TableExtension;

use LoversOfBehat\TableExtension\Event\TableEventInterface;

/**
 * Interface for dispatching events.
 */
interface TableEventDispatcherInterface
{

    /**
     * Dispatches an event.
     *
     * @param TableEventInterface $event
     *   The event to dispatch.
     */
    public function dispatch(TableEventInterface $event): void;
}
