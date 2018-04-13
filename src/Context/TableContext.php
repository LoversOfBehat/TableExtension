<?php

declare(strict_types = 1);

namespace OpenEuropa\TableContext\Context;

use Behat\Mink\Element\NodeElement;
use Behat\MinkExtension\Context\RawMinkContext;
use OpenEuropa\TableContext\Table;

class TableContext extends RawMinkContext
{

    /**
     * An associative array of table selectors, keyed by table name.
     *
     * @var array
     */
    protected $tableMap;

    /**
     * Constructs a new TableContext object.
     *
     * @param array $tableMap
     *   Optional associative array of table CSS selectors, keyed by table name.
     */
    public function __construct(array $tableMap = [])
    {
        $this->tableMap = $tableMap;
    }

    /**
     * Checks that there is at least 1 table on the page.
     *
     * @Then I should see a table
     */
    public function assertTable(): void
    {
        if ($this->getTableCount()) {
            return;
        }
        throw new \RuntimeException('There are no tables present on the page.');
    }

    /**
     * Checks that there are no tables on the page.
     *
     * @Then I should not see a table
     */
    public function assertNoTable(): void
    {
        $count = $this->getTableCount();
        if ($count === 0) {
            return;
        }
        throw new \RuntimeException("There are $count tables on the page, but none should be present.");
    }

    /**
     * Checks that the expected number of tables is present in the page.
     *
     * @Then /^I should see (\d+) (?:table|tables)$/
     */
    public function assertTables(int $count): void
    {
        $actual = $this->getTableCount();
        if ($count === $actual) {
            return;
        }
        throw new \RuntimeException("There are $actual tables on the page instead of the expected $count.");
    }

    /**
     * Checks that a table exists in the page with the given number of columns.
     *
     * @param int $count
     *   The expected number of columns.
     *
     * @Then I should see a table with :count column(s)
     */
    public function assertColumnCount(int $count): void
    {
        $this->assertTable();
        foreach ($this->getTables() as $table) {
            if ($table->getColumnCount() === $count) {
                return;
            }
        }
        throw new \RuntimeException("No table with $count columns is present on the page.");
    }

    /**
     * Checks that a table with the given number of columns does not exist in the page.
     *
     * @param int $count
     *   The number of columns.
     *
     * @Then I should not see a table with :count column(s)
     */
    public function assertNoColumnCount(int $count): void
    {
        foreach ($this->getTables() as $table) {
            if ($table->getColumnCount() === $count) {
                throw new \RuntimeException("A table with $count columns is present on the page, but should not be.");
            }
        }
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
            return new Table($this->getSession(), $element);
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
