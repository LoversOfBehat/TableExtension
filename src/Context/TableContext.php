<?php

declare(strict_types = 1);

namespace LoversOfBehat\TableExtension\Context;

use Behat\Gherkin\Node\TableNode;
use LoversOfBehat\TableExtension\AssertArraySubset;
use LoversOfBehat\TableExtension\Exception\NoArraySubsetException;
use LoversOfBehat\TableExtension\Exception\TableNotFoundException;

class TableContext extends RawTableContext
{

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
     * Checks that the given table is present on the page.
     *
     * @Then I should see the :name table
     */
    public function assertNamedTable(string $name): void
    {
        $this->getTable($name);
    }

    /**
     * Checks that the given table is not present on the page.
     *
     * @Then I should not see the :name table
     */
    public function assertNoNamedTable(string $name): void
    {
        try {
            $this->getTable($name);
        } catch (TableNotFoundException $e) {
            return;
        }
        throw new \RuntimeException("The $name table was found in the page but it was not expected to be.");
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
    public function assertTableWithColumnCountExists(int $count): void
    {
        $this->assertTable();
        foreach ($this->getTables() as $table) {
            if ($table->getColumnCount() === $count) {
                return;
            }
        }
        throw new TableNotFoundException("No table with $count columns is present on the page.");
    }

    /**
     * Checks that a table exists in the page with the given number of rows.
     *
     * @param int $count
     *   The expected number of rows.
     *
     * @Then I should see a table with :count row(s)
     */
    public function assertTableWithRowCountExists(int $count): void
    {
        $this->assertTable();
        foreach ($this->getTables() as $table) {
            if ($table->getRowCount() === $count) {
                return;
            }
        }
        throw new \RuntimeException("No table with $count rows is present on the page.");
    }

    /**
     * Checks that the given table has the given number of columns.
     *
     * @param string $name
     *   The human readable name for the table.
     * @param int $count
     *   The expected number of columns.
     *
     * @Then the :name table should have :count column(s)
     */
    public function assertTableColumnCount(string $name, int $count): void
    {
        $table = $this->getTable($name);
        $actual = $table->getColumnCount();
        if ($actual === $count) {
            return;
        }
        throw new \RuntimeException("The $name table should have $count columns but it has $actual columns.");
    }

    /**
     * Checks that the given table does not have the given number of columns.
     *
     * @param string $name
     *   The human readable name for the table.
     * @param int $count
     *   The unexpected number of columns.
     *
     * @Then the :name table should not have :count column(s)
     */
    public function assertNoTableColumnCount(string $name, int $count): void
    {
        $table = $this->getTable($name);
        $actual = $table->getColumnCount();
        if ($actual === $count) {
            throw new \RuntimeException("The $name table should not have $count columns.");
        }
    }

    /**
     * Checks that the given table has the given number of rows.
     *
     * @param string $name
     *   The human readable name for the table.
     * @param int $count
     *   The expected number of rows.
     *
     * @Then the :name table should have :count row(s)
     */
    public function assertTableRowCount(string $name, int $count): void
    {
        $table = $this->getTable($name);
        $actual = $table->getRowCount();
        if ($actual === $count) {
            return;
        }
        throw new \RuntimeException("The $name table should have $count rows but it has $actual rows.");
    }

    /**
     * Checks that the given table does not have the given number of rows.
     *
     * @param string $name
     *   The human readable name for the table.
     * @param int $count
     *   The unexpected number of rows.
     *
     * @Then the :name table should not have :count row(s)
     */
    public function assertNoTableRowCount(string $name, int $count): void
    {
        $table = $this->getTable($name);
        $actual = $table->getRowCount();
        if ($actual === $count) {
            throw new \RuntimeException("The $name table should not have $count rows.");
        }
    }

    /**
     * Checks that the given table contains the given data.
     *
     * This checks that the data is present in the table, ignoring row and column ordering.
     *
     * @param string $name
     *   The human readable name for the table.
     * @param TableNode $data
     *   The data that is expected to be present in the table.
     *
     * @Then the :name table should contain:
     */
    public function assertTableData(string $name, TableNode $data): void
    {
        $table = $this->getTable($name);
        $this->assertArraySubset($data->getRows(), $table->getData());
    }

    /**
     * Checks that the given table does not contain the given data.
     *
     * This checks that the data is not present in the table, ignoring row and column ordering.
     *
     * @param string $name
     *   The human readable name for the table.
     * @param TableNode $data
     *   The data that is expected to be present in the table.
     *
     * @Then the :name table should not contain:
     */
    public function assertNoTableData(string $name, TableNode $data): void
    {
        try {
            $this->assertTableData($name, $data);
            throw new \RuntimeException("A table with the given data is present on the page, but should not be.");
        } catch (NoArraySubsetException $e) {
        }
    }

    /**
     * Checks that the given table contains the given non-consecutive columns, identified by headers.
     *
     * @param string $name
     *   The human readable name for the table.
     * @param TableNode $data
     *   The data that is expected to be present in the table, with the first row identifying the columns to match.
     *
     * @Then the :name table should contain the following column(s):
     */
    public function assertTableColumnData(string $name, TableNode $data): void
    {
        $table = $this->getTable($name);
        $this->assertArraySubset($data->getColumnsHash(), array_values($table->getColumnData($data->getRow(0))));
    }

    /**
     * Checks that the given table does not contain the given non-consecutive columns, identified by headers.
     *
     * @param string $name
     *   The human readable name for the table.
     * @param TableNode $data
     *   The data that should not be present in the table, with the first row identifying the columns to match.
     *
     * @Then the :name table should not contain the following column(s):
     */
    public function assertNoTableColumnData(string $name, TableNode $data): void
    {
        try {
            $this->assertTableColumnData($name, $data);
            throw new \RuntimeException("A table with the given column data is present on the page, but should not be.");
        } catch (NoArraySubsetException $e) {
        }
    }

    /**
     * Checks that the given table contains the given non-consecutive rows, identified by headers.
     *
     * @param string $name
     *   The human readable name for the table.
     * @param TableNode $data
     *   The data that is expected to be present in the table, with the first column identifying the rows to match.
     *
     * @Then the :name table should contain the following row(s):
     */
    public function assertTableRowData(string $name, TableNode $data): void
    {
        $table = $this->getTable($name);
        $this->assertArraySubset($data->getRowsHash(), $table->getRowData($data->getColumn(0)));
    }

    /**
     * Checks that the given table does not contain the given non-consecutive rows, identified by headers.
     *
     * @param string $name
     *   The human readable name for the table.
     * @param TableNode $data
     *   The data that should not be present in the table, with the first column identifying the rows to match.
     *
     * @Then the :name table should not contain the following row(s):
     */
    public function assertNoTableRowData(string $name, TableNode $data): void
    {
        try {
            $this->assertTableRowData($name, $data);
        } catch (NoArraySubsetException $e) {
            return;
        }
        throw new \RuntimeException("A table with the given row data is present in the page but it was not expected to be.");
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
     * Checks that a table with the given number of rows does not exist in the page.
     *
     * @param int $count
     *   The number of rows.
     *
     * @Then I should not see a table with :count row(s)
     */
    public function assertNoRowCount(int $count): void
    {
        foreach ($this->getTables() as $table) {
            if ($table->getRowCount() === $count) {
                throw new \RuntimeException("A table with $count rows is present on the page, but should not be.");
            }
        }
    }

    /**
     * Checks that the given array contains the given subset.
     *
     * @param array $subset
     *   The subset that is expected to exist in the array.
     * @param array $array
     *   The array.
     * @param bool $strict
     *   Whether to perform strict data type checking when comparing the two arrays.
     *
     * @throws NoArraySubsetException
     */
    protected function assertArraySubset(array $subset, array $array, bool $strict = false): void
    {
        $assert = new AssertArraySubset($subset, $strict);
        $assert->evaluate($array);
    }
}
