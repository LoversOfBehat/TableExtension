<?php

declare(strict_types = 1);

namespace LoversOfBehat\TableExtension;

use Behat\Behat\Context\Context;
use Behat\Testwork\Environment\Environment;
use Behat\Testwork\Hook\HookDispatcher;
use LoversOfBehat\TableExtension\Event\TableEventInterface;
use LoversOfBehat\TableExtension\Hook\Scope\AfterTableFetchScope;

/**
 * Dispatches events using Behat's hook dispatcher.
 */
class BehatHookTableEventDispatcher implements TableEventDispatcherInterface
{

    /**
     * The Behat test environment.
     *
     * @var Environment
     */
    protected $environment;

    /**
     * The Behat context.
     *
     * @var Context
     */
    protected $context;

    /**
     * Behat's hook dispatcher.
     *
     * @var HookDispatcher
     */
    protected $dispatcher;

    /**
     * Constructs a new BehatHookTableEventDispatcher.
     *
     * @param Environment $environment
     *   The Behat test environment.
     * @param Context $context
     *   The Behat context.
     * @param HookDispatcher $dispatcher
     *   Behat's hook dispatcher.
     */
    public function __construct(Environment $environment, Context $context, HookDispatcher $dispatcher)
    {
        $this->environment = $environment;
        $this->context = $context;
        $this->dispatcher = $dispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function dispatch(TableEventInterface $event): void
    {
        $scope = new AfterTableFetchScope($this->environment, $this->context, $event->getHtmlContainer());
        $this->dispatcher->dispatchScopeHooks($scope);
    }
}
