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
     * The Mink element representing the table.
     *
     * @var NodeElement
     */
    protected $element;

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
     * @param NodeElement $element
     *   The Mink element representing the table.
     */
    public function __construct(Session $session, NodeElement $element)
    {
        $this->session = $session;
        $this->element = $element;
    }

    /**
     * Returns the number of columns.
     *
     * @return int
     */
    public function getColumnCount(): int
    {
        // According to the HTML5 spec, a colspan should be a valid positive integer, greater than zero.
        // @see https://www.w3.org/TR/html/tabular-data.html#attributes-common-to-td-and-th-elements
        $is_positive_integer = function ($value): bool {
            return filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) !== false;
        };

        $count = 0;
        $expression = '(//tr)[1]/*[name()="th" or name()="td"]';
        /** @var NodeElement $cell */
        foreach ($this->element->findAll('xpath', $expression) as $cell) {
            $colspan = $cell->getAttribute('colspan');
            if ($colspan !== null) {
                if (!$is_positive_integer($colspan)) {
                    // @codingStandardsIgnoreStart
                    throw new \RuntimeException('Cannot calculate column count. One of the table cells has a colspan that is not a valid positive integer greater than zero.');
                    // @codingStandardsIgnoreEnd
                }
                $count += $colspan;
            } else {
                $count++;
            }
        }
        return $count;
    }

    /**
     * Returns the table data in a 2-dimensional array.
     *
     * @return array
     */
    public function getData(): array
    {
        if (isset($this->data)) {
            return $this->data;
        }

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
                        // Leave spanned cells empty.
                        $value = $i == 0 && $j == 0 ? $cell->nodeValue : '';
                        $this->data[$current_row + $i][$current_column + $j] = $value;
                    }
                }
                $current_column++;
            }
            $current_row++;
        }

        return $this->data;
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
                $html = $this->session->getDriver()->getOuterHtml($this->element->getXpath());
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
