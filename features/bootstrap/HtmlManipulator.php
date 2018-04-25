<?php

declare(strict_types = 1);

use Symfony\Component\DomCrawler\Crawler;

/**
 * Illustrates a way to manipulate HTML documents by leveraging the Symfony DOM Crawler component.
 *
 * This is used by the @AfterTableFetch example hook implementation.
 *
 * @see FeatureContext::stripScreenReaderElements()
 */
class HtmlManipulator extends Crawler
{

    /**
     * Removes the elements that match the given CSS selector from the document.
     *
     * @param string $selector
     *   The CSS selector.
     *
     * @return HtmlManipulator
     */
    public function removeElements(string $selector): self
    {
        /** @var DOMElement $node */
        foreach ($this->filter($selector) as $node) {
            $node->parentNode->removeChild($node);
        }
        return $this;
    }

}
