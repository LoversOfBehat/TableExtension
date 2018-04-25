<?php

declare(strict_types = 1);

namespace OpenEuropa\TableExtension\Event;

use OpenEuropa\TableExtension\HtmlContainer;

/**
 * Interface for events that involve HTML5 tables.
 */
interface TableEventInterface
{

    /**
     * Returns an object that contains the HTML of the fetched table.
     *
     * @return HtmlContainer
     */
    public function getHtmlContainer(): HtmlContainer;
}
