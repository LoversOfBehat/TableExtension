<?php

declare(strict_types = 1);

namespace LoversOfBehat\TableExtension\Context;

use Behat\Mink\Element\NodeElement;
use Behat\MinkExtension\Context\RawMinkContext;
use Behat\Testwork\Environment\Environment;
use Behat\Testwork\Hook\HookDispatcher;
use LoversOfBehat\TableExtension\BehatHookTableEventDispatcher;
use LoversOfBehat\TableExtension\EnvironmentContainer;
use LoversOfBehat\TableExtension\Exception\TableNotFoundException;
use LoversOfBehat\TableExtension\Table;
use LoversOfBehat\TableExtension\TableEventDispatcherInterface;

class RawTableContext extends RawMinkContext implements TableAwareInterface
{

    /**
     * An associative array of table selectors, keyed by table name.
     *
     * @var array
     */
    protected $tableMap;

    /**
     * Event dispatcher.
     *
     * @var \Behat\Testwork\Hook\HookDispatcher
     */
    protected $dispatcher;

    /**
     * The container that references the Behat test environment.
     *
     * @var EnvironmentContainer
     */
    protected $environmentContainer;

    /**
     * {@inheritdoc}
     */
    public function getDispatcher(): HookDispatcher
    {
        return $this->dispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function setDispatcher(HookDispatcher $dispatcher): void
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * Returns the Behat test environment.
     *
     * @return Environment
     */
    public function getEnvironment(): Environment
    {
        return $this->environmentContainer->getEnvironment();
    }

    /**
     * {@inheritdoc}
     */
    public function setEnvironmentContainer(EnvironmentContainer $container): void
    {
        $this->environmentContainer = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function setTableMap(array $tableMap): void
    {
        $this->tableMap = $tableMap;
    }

    /**
     * Returns the table that corresponds with the given human readable name.
     *
     * @param string $name
     *   The human readable name for the table.
     *
     * @return Table
     */
    protected function getTable(string $name): Table
    {
        if (!array_key_exists($name, $this->tableMap)) {
            throw new \RuntimeException("The '$name' table is not defined in behat.yml.");
        }
        $selector = $this->tableMap[$name];
        $element = $this->getSession()->getPage()->find('css', $selector);

        if (empty($element)) {
            throw new TableNotFoundException("The '$name' table is not found in the page.");
        }

        $tag_name = $element->getTagName();
        if ($tag_name !== 'table') {
            throw new \RuntimeException("The '$name' element is not a table but a $tag_name.");
        }

        return $this->createTable($element->getXpath());
    }

    /**
     * Returns the tables that are present in the page.
     *
     * @return Table[]
     *   An array of tables.
     */
    protected function getTables(): array
    {
        return array_map(function (NodeElement $element): Table {
            return $this->createTable($element->getXpath());
        }, $this->getSession()->getPage()->findAll('css', 'table'));
    }

    /**
     * Returns a freshly instantiated table object for the given XPath expression.
     *
     * @param string $xpath
     *   The XPath expression that identifies the table in the document.
     *
     * @return Table
     */
    protected function createTable(string $xpath): Table
    {
        return new Table($this->getSession()->getDriver(), $this->createTableDispatcher(), $xpath);
    }

    /**
     * Returns a freshly instantiated event dispatcher.
     *
     * @return TableEventDispatcherInterface
     */
    protected function createTableDispatcher(): TableEventDispatcherInterface
    {
        return new BehatHookTableEventDispatcher($this->getEnvironment(), $this, $this->getDispatcher());
    }

    /**
     * Returns the number of tables that are present in the page.
     *
     * @return int
     */
    protected function getTableCount(): int
    {
        return count($this->getTables());
    }
}
