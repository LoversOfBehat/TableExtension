<?php

declare(strict_types = 1);

namespace OpenEuropa\TableExtension\Context;

use Behat\Behat\Context\Context;
use Behat\Testwork\Hook\HookDispatcher;

interface TableAwareInterface extends Context
{

    /**
     * Sets the event dispatcher.
     *
     * @param HookDispatcher $dispatcher
     */
    public function setDispatcher(HookDispatcher $dispatcher): void;

    /**
     * Sets the table map.
     *
     * @param array $tableMap
     */
    public function setTableMap(array $tableMap): void;
}
