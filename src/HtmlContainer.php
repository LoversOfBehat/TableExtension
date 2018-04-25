<?php

declare(strict_types = 1);

namespace OpenEuropa\TableExtension;

/**
 * Class that contains a HTML string.
 */
class HtmlContainer
{

    /**
     * The HTML.
     *
     * @var string
     */
    protected $html;

    /**
     * Constructs a new HtmlContainer object.
     *
     * @param string $html
     */
    public function __construct(string $html)
    {
        $this->setHtml($html);
    }

    /**
     * Returns the HTML.
     *
     * @return string
     */
    public function getHtml(): string
    {
        return $this->html;
    }

    /**
     * Sets the HTML.
     *
     * @param string $html
     */
    public function setHtml(string $html): void
    {
        $this->html = $html;
    }
}
