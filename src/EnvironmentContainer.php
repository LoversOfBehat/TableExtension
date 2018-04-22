<?php

declare(strict_types = 1);

namespace OpenEuropa\TableExtension;

use Behat\Testwork\Environment\Environment;

/**
 * Service that contains the current Behat test environment.
 *
 * This allows the Behat test environment to be accessed in context classes.
 */
class EnvironmentContainer
{
    /**
     * The Behat test environment.
     *
     * @var Environment
     */
    protected $environment;

    /**
     * Returns the test environment.
     *
     * @return Environment
     */
    public function getEnvironment(): Environment
    {
        return $this->environment;
    }

    /**
     * Sets the test environment.
     *
     * @param Environment $environment
     */
    public function setEnvironment(Environment $environment): void
    {
        $this->environment = $environment;
    }
}
