<?php

declare(strict_types = 1);

namespace OpenEuropa\TableExtension\Context\Initializer;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\Initializer\ContextInitializer;
use Behat\Testwork\Hook\HookDispatcher;
use OpenEuropa\TableExtension\Context\TableAwareInterface;
use OpenEuropa\TableExtension\EnvironmentContainer;

class TableAwareInitializer implements ContextInitializer
{

    /**
     * The dispatcher for Behat hooks.
     *
     * @var \Behat\Testwork\Hook\HookDispatcher
     */
    protected $dispatcher;

    /**
     * The service that references the Behat test environment.
     *
     * @var EnvironmentContainer
     */
    protected $container;

    /**
     * The TableContext configuration array.
     *
     * @var array
     */
    protected $config;

    /**
     * Constructs a new TableAwareInitializer object.
     *
     * @param \Behat\Testwork\Hook\HookDispatcher $dispatcher
     *   The Behat hook dispatcher.
     * @param EnvironmentContainer $container
     *   The service that references the Behat test environment.
     * @param array $config
     *   The configuration for the extension, as defined in behat.yml.
     */
    public function __construct(HookDispatcher $dispatcher, EnvironmentContainer $container, array $config)
    {
        $this->dispatcher = $dispatcher;
        $this->container = $container;
        $this->config = $config;
    }

    /**
     * {@inheritdocs}
     */
    public function initializeContext(Context $context): void
    {
        if (!$context instanceof TableAwareInterface) {
            return;
        }
        $context->setDispatcher($this->dispatcher);
        $context->setEnvironmentContainer($this->container);
        $table_map = !empty($this->config['table_map']) ? $this->config['table_map'] : [];
        $context->setTableMap($table_map);
    }
}
