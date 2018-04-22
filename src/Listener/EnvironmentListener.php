<?php

namespace OpenEuropa\TableExtension\Listener;

use Behat\Behat\EventDispatcher\Event\BeforeFeatureTested;
use Behat\Behat\EventDispatcher\Event\FeatureTested;
use OpenEuropa\TableExtension\EnvironmentContainer;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Event listener that captures the Behat test environment used to execute a scenario.
 *
 * The environment is needed to instantiate the hook scope when firing hooks, but only becomes available when tests are
 * being executed. This listener is invoked when a scenario is being tested, and it stores the test environment in a
 * service singleton.
 */
class EnvironmentListener implements EventSubscriberInterface
{

    /**
     * The service that references the Behat test environment.
     *
     * @var EnvironmentContainer
     */
    protected $container;

    /**
     * Constructs a new EnvironmentListener object.
     *
     * @param EnvironmentContainer $container
     *   The service that references the Behat test environment.
     */
    public function __construct(EnvironmentContainer $container)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [FeatureTested::BEFORE => ['captureEnvironment', 11]];
    }

    /**
     * Captures the Behat test environment from the event and stores it so it can be used in our context.
     *
     * @param BeforeFeatureTested $event
     */
    public function captureEnvironment(BeforeFeatureTested $event)
    {
        $this->container->setEnvironment($event->getEnvironment());
    }
}
