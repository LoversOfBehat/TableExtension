<?php

declare(strict_types = 1);

namespace OpenEuropa\TableExtension\Context;

use Behat\Behat\Context\Context;
use Behat\Testwork\Hook\HookDispatcher;
use OpenEuropa\TableExtension\EnvironmentContainer;

interface TableAwareInterface extends Context
{

    /**
     * Gets the event dispatcher.
     *
     * @return HookDispatcher
     */
    public function getDispatcher(): HookDispatcher;

    /**
     * Sets the event dispatcher.
     *
     * @param HookDispatcher $dispatcher
     */
    public function setDispatcher(HookDispatcher $dispatcher): void;

    /**
     * Sets the environment container.
     *
     * @param EnvironmentContainer $container
     */
    public function setEnvironmentContainer(EnvironmentContainer $container): void;

    /**
     * Sets the table map.
     *
     * @param array $tableMap
     */
    public function setTableMap(array $tableMap): void;
}
