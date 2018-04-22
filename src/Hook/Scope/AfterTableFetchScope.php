<?php

declare(strict_types = 1);

namespace OpenEuropa\TableExtension\Hook\Scope;

use Behat\Behat\Context\Context;
use Behat\Testwork\Environment\Environment;
use OpenEuropa\TableExtension\HtmlContainer;

/**
 * Hook scope for hooks that concern HTML5 tables.
 */
class AfterTableFetchScope implements TableScopeInterface
{

    const SCOPE = 'table.fetch.after';

    /**
     * The Behat test environment.
     *
     * @var Environment
     */
    protected $environment;

    /**
     * The Behat context.
     *
     * @var Context
     */
    protected $context;

    /**
     * An object containing the HTML for the table which has been retrieved from the page.
     *
     * @var HtmlContainer
     */
    protected $htmlContainer;

    /**
     * Constructs a new AfterTableFetchScope object.
     *
     * @param Environment $environment
     *   The Behat test environment.
     * @param Context $context
     *   The Behat context.
     * @param HtmlContainer $htmlContainer
     *   The HTML for the table which has been retrieved from the page.
     */
    public function __construct(Environment $environment, Context $context, HtmlContainer $htmlContainer)
    {
        $this->environment = $environment;
        $this->context = $context;
        $this->htmlContainer = $htmlContainer;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return self::SCOPE;
    }

    /**
     * {@inheritdoc}
     */
    public function getSuite()
    {
        return $this->environment->getSuite();
    }

    /**
     * {@inheritdoc}
     */
    public function getEnvironment()
    {
        return $this->environment;
    }

    /**
     * {@inheritdoc}
     */
    public function getHtml(): string
    {
        return $this->htmlContainer->getHtml();
    }

    /**
     * {@inheritdoc}
     */
    public function setHtml(string $html): void
    {
        $this->htmlContainer->setHtml($html);
    }
}
