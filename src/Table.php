<?php

declare(strict_types = 1);

namespace OpenEuropa\TableContext;

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\DriverException;
use Behat\Mink\Exception\UnsupportedDriverActionException;
use Behat\Mink\Session;
use Symfony\Component\DomCrawler\Crawler;

/**
 * An object that represents an HTML table that has been found in the page.
 */
class Table
{

    /**
     * The Mink session.
     *
     * @var Session
     */
    protected $session;

    /**
     * The XPath expression that can be used to retrieve the table.
     *
     * @var string
     */
    protected $xpath;

    /**
     * The table wrapped in a Crawler for easy traversing.
     *
     * @var Crawler
     */
    protected $crawler;

    /**
     * The plain table data in a flat array.
     *
     * @var array
     */
    protected $data;

    /**
     * Constructs a new Table.
     *
     * @param Session $session
     *   The Mink session.
     * @param string $xpath
     *   The XPath expression that can be used to retrieve the table.
     */
    public function __construct(Session $session, string $xpath)
    {
        $this->session = $session;
        $this->xpath = $xpath;
    }

    /**
     * Returns the number of columns.
     *
     * @return int
     */
    public function getColumnCount(): int
    {
        $data = $this->getData();
        $row = reset($data);
        return count($row);
    }

    /**
     * Returns the number of rows.
     *
     * @return int
     */
    public function getRowCount(): int
    {
        $data = $this->getData();
        $data = array_map(null, ...$data);
        $row = reset($data);
        return count($row);
    }

    /**
     * Returns the table data in a 2-dimensional array.
     *
     * @param bool $include_metadata
     *   Whether to include metadata, such as the cell type and the parent cell for spanned cells.
     * @param bool $filter_header_rows
     *   Whether to filter out any rows that only contain header cells.
     * @param bool $filter_header_columns
     *   Whether to filter out any columns that only contain header cells.
     *
     * @return array[]
     */
    public function getData(bool $include_metadata = false, bool $filter_header_rows = false, bool $filter_header_columns = false): array
    {
        if (!isset($this->data)) {
            $this->populateData();
        }
        $data = $this->data;

        // Anonymous function that filters out rows containing only header cells.
        $filter_headers = function (array $data): array {
            return array_filter($data, function (array $row): bool {
                foreach ($row as $cell) {
                    if ($cell['type'] === 'td') {
                        return true;
                    }
                }
                return false;
            });
        };

        if ($filter_header_rows) {
            $data = $filter_headers($data);
        }

        if ($filter_header_columns) {
            // Rotate the array (mapping rows to columns), then apply the filter and rotate back.
            $data = array_map(null, ...$data);
            $data = $filter_headers($data);
            $data = array_map(null, ...$data);
        }

        if ($include_metadata) {
            return $data;
        }

        // Filter out the metadata, keeping only the raw values.
        foreach ($data as $key => $row) {
            $data[$key] = array_map(function (array $cell): string {
                return (string) trim($cell['value']);
            }, $row);
        }
        return $data;
    }

    /**
     * Populates the internal data array from the table HTML.
     */
    protected function populateData(): void
    {
        $this->data = [];

        $current_row = 0;
        foreach ($this->getCrawler()->evaluate('//tr') as $row) {
            $current_column = 0;
            if (!$row instanceof Crawler) {
                $row = new Crawler($row);
            }

            /** @var \DOMElement $cell */
            foreach ($row->evaluate('./*[name()="th" or name()="td"]') as $cell) {
                // If the data for this cell is already populated by a spanned cell, skip to the next available cell.
                while (isset($this->data[$current_row][$current_column])) {
                    $current_column++;
                }

                // Apply the data to the cell(s), taking into account any rowspans and colspans.
                $colspan = (int) $cell->getAttribute('colspan') ?: 1;
                $rowspan = (int) $cell->getAttribute('rowspan') ?: 1;
                for ($i = 0; $i < $rowspan; $i++) {
                    for ($j = 0; $j < $colspan; $j++) {
                        $is_spanned = $i != 0 || $j != 0;
                        $this->data[$current_row + $i][$current_column + $j] = [
                            // Leave spanned cells empty.
                            'value' => $is_spanned ? '' : $cell->nodeValue,
                            'type' => $cell->nodeName,
                            'spanSourceCell' => $is_spanned ? [$current_row, $current_column] : null,
                        ];
                    }
                }
                $current_column++;
            }
            $current_row++;
        }
    }

    /**
     * Returns the data from the columns identified by the given headers.
     *
     * Any rows that only contain header cells are excluded from the result.
     *
     * @param array $headers
     *   A 1-dimensional array containing the header values for which to return the columns.
     *
     * @return array[][]
     *   An array of column data, each value an associative array of values keyed by column header.
     */
    public function getColumnData(array $headers): array
    {
        $column_keys = $this->getColumnKeys($headers);
        $data = $this->getData(false, true);

        // Filter out the unwanted columns.
        foreach ($data as &$row) {
            $row = array_filter($row, function (int $key) use ($column_keys): bool {
                return in_array($key, $column_keys);
            }, ARRAY_FILTER_USE_KEY);
        }

        // Apply the column keys to the data.
        $keys = array_keys($column_keys);
        return array_map(function (array $values) use ($keys): array {
            return array_combine($keys, $values);
        }, $data);
    }

    /**
     * Returns the data from the rows identified by the given headers.
     *
     * Any columns that only contain header cells are excluded from the result.
     *
     * @param array $headers
     *   A 1-dimensional array containing the header values for which to return the rows.
     *
     * @return array[][]
     *   An array of row data, each value an associative array of values keyed by row header.
     */
    public function getRowData(array $headers): array
    {
        $row_keys = $this->getRowKeys($headers);
        $data = $this->getData(false, false, true);

        // Filter out the unwanted rows.
        $data = array_filter($data, function (int $key) use ($row_keys): bool {
            return in_array($key, $row_keys);
        }, ARRAY_FILTER_USE_KEY);

        // Apply the row keys to the data.
        $keys = array_keys($row_keys);
        return array_combine($keys, $data);
    }

    /**
     * Returns the keys of the columns that match the given header values.
     *
     * @param string[] $header_values
     *   An array of header cell values that are used to identify the columns.
     *
     * @return int[]
     *   An associative array of column keys, keyed by header value.
     */
    public function getColumnKeys(array $header_values): array
    {
        $headers = $this->getColumnHeaders();

        $keys = [];
        foreach ($header_values as $header_value) {
            if ((string) $header_value != $header_value) {
                throw new \InvalidArgumentException('The passed in headers should be an array of string-like values');
            }
            $result = array_keys(array_filter($headers, function (array $column) use ($header_value): bool {
                return in_array($header_value, $column);
            }));
            if (empty($result)) {
                throw new \RuntimeException("None of the columns in the table contains the header '$header_value'.");
            }
            if (count($result) > 1) {
                throw new \RuntimeException("There are multiple columns in the table that contain the header '$header_value'.");
            }
            $keys[$header_value] = reset($result);
        }

        return $keys;
    }

    /**
     * Returns the keys of the rows that match the given header values.
     *
     * @param string[] $header_values
     *   An array of header cell values that are used to identify the rows.
     *
     * @return int[]
     *   An associative array of row keys, keyed by header value.
     */
    public function getRowKeys(array $header_values): array
    {
        $headers = $this->getRowHeaders();

        $keys = [];
        foreach ($header_values as $header_value) {
            if ((string) $header_value != $header_value) {
                throw new \InvalidArgumentException('The passed in headers should be an array of string-like values');
            }
            $result = array_keys(array_filter($headers, function (array $row) use ($header_value): bool {
                return in_array($header_value, $row);
            }));
            if (empty($result)) {
                throw new \RuntimeException("None of the rows in the table contains the header '$header_value'.");
            }
            if (count($result) > 1) {
                throw new \RuntimeException("There are multiple rows in the table that contain the header '$header_value'.");
            }
            $keys[$header_value] = reset($result);
        }

        return $keys;
    }

    /**
     * Returns an array of header cell values ordered by columns.
     *
     * @return array[]
     *   An array, each element an array containing the element texts of the header cells in the column.
     */
    public function getColumnHeaders(): array
    {
        $data = $this->getData(true);
        $column_count = $this->getColumnCount();
        $headers = [];

        for ($i = 0; $i < $column_count; $i++) {
            $headers[$i] = [];
            foreach (array_column($data, $i) as $cell) {
                if ($cell['type'] === 'th' && !empty($cell['value'])) {
                    $headers[$i][] = $cell['value'];
                }
            }
        }

        return $headers;
    }

    /**
     * Returns an array of header cell values ordered by rows.
     *
     * @return array[]
     *   An array, each element an array containing the element texts of the header cells in the row.
     */
    public function getRowHeaders(): array
    {
        $headers = [];

        foreach ($this->getData(true) as $i => $row) {
            $headers[$i] = [];
            foreach ($row as $j => $cell) {
                if ($cell['type'] === 'th' && !empty($cell['value'])) {
                    $headers[$i][$j] = $cell['value'];
                }
            }
        }

        return $headers;
    }

    /**
     * Returns a Crawler object containing the table.
     *
     * @return Crawler
     */
    protected function getCrawler(): Crawler
    {
        if (!isset($this->crawler)) {
            // To speed up processing of large tables, retrieve the full HTML from the browser in a single operation.
            try {
                $html = $this->session->getDriver()->getOuterHtml($this->xpath);
            } catch (UnsupportedDriverActionException $e) {
                // @todo Implement an alternative approach that uses DriverInterface::find().
                throw new \RuntimeException('Driver doesn\'t support direct retrieval of HTML data.', 0, $e);
            } catch (DriverException $e) {
                throw new \RuntimeException('An error occurred while retrieving table data.', 0, $e);
            }

            $this->crawler = new Crawler($html);
        }
        return $this->crawler;
    }
}
