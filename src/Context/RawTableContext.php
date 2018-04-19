<?php

declare(strict_types = 1);

namespace OpenEuropa\TableExtension\Context;

use Behat\Mink\Element\NodeElement;
use Behat\MinkExtension\Context\RawMinkContext;
use Behat\Testwork\Hook\HookDispatcher;
use OpenEuropa\TableExtension\Exception\TableNotFoundException;
use OpenEuropa\TableExtension\Table;

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
     * {@inheritdoc}
     */
    public function setDispatcher(HookDispatcher $dispatcher): void
    {
        $this->dispatcher = $dispatcher;
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

        return new Table($this->getSession(), $element->getXpath());
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
            return new Table($this->getSession(), $element->getXpath());
        }, $this->getSession()->getPage()->findAll('css', 'table'));
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
