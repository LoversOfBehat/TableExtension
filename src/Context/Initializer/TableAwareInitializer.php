<?php

declare(strict_types = 1);

namespace OpenEuropa\TableExtension\Context\Initializer;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\Initializer\ContextInitializer;
use Behat\Testwork\Hook\HookDispatcher;
use OpenEuropa\TableExtension\Context\TableAwareInterface;

class TableAwareInitializer implements ContextInitializer
{

    /**
     * The dispatcher for Behat hooks.
     *
     * @var \Behat\Testwork\Hook\HookDispatcher
     */
    protected $dispatcher;

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
     */
    public function __construct(HookDispatcher $dispatcher, array $config)
    {
        $this->dispatcher = $dispatcher;
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
        $table_map = !empty($this->config['table_map']) ? $this->config['table_map'] : [];
        $context->setTableMap($table_map);
    }
}
