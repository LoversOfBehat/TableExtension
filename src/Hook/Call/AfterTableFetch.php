<?php

declare(strict_types = 1);

namespace LoversOfBehat\TableExtension\Hook\Call;

use Behat\Testwork\Hook\Call\RuntimeFilterableHook;
use Behat\Testwork\Hook\Scope\HookScope;
use LoversOfBehat\TableExtension\Hook\Scope\AfterTableFetchScope;

class AfterTableFetch extends RuntimeFilterableHook
{

    const NAME = 'AfterTableFetch';

    /**
     * Constructs a new AfterTableFetch object.
     *
     * @param $filterString
     * @param $callable
     * @param null $description
     */
    public function __construct($filterString, $callable, $description = null)
    {
        parent::__construct(AfterTableFetchScope::SCOPE, $filterString, $callable, $description);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return self::NAME;
    }

    /**
     * {@inheritdoc}
     */
    public function filterMatches(HookScope $scope)
    {
        return true;
    }
}
