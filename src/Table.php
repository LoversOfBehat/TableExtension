<?php

declare(strict_types = 1);

namespace OpenEuropa\TableContext;

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Session;

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
}
