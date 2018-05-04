<?php

declare(strict_types = 1);

namespace LoversOfBehat\TableExtension\Hook\Scope;

use Behat\Testwork\Hook\Scope\HookScope;

/**
 * Interface for hook scopes that deal with HTML5 tables.
 */
interface TableScopeInterface extends HookScope
{

    /**
     * Returns the HTML for the table that was retrieved from the page.
     *
     * @return string
     */
    public function getHtml(): string;

    /**
     * Sets the updated table HTML.
     *
     * @param string $html
     */
    public function setHtml(string $html): void;
}
