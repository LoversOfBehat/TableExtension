<?php

declare(strict_types = 1);

namespace OpenEuropa\TableExtension\ServiceContainer;

use Behat\Behat\Context\ServiceContainer\ContextExtension;
use Behat\Behat\EventDispatcher\ServiceContainer\EventDispatcherExtension;
use Behat\Testwork\ServiceContainer\Extension as ExtensionInterface;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use OpenEuropa\TableExtension\Context\Initializer\TableAwareInitializer;
use OpenEuropa\TableExtension\EnvironmentContainer;
use OpenEuropa\TableExtension\Hook\Context\Annotation\HookAnnotationReader;
use OpenEuropa\TableExtension\Listener\EnvironmentListener;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class TableExtension implements ExtensionInterface
{

    const CONFIG_KEY = 'table';

    /**
     * {@inheritdoc}
     */
    public function configure(ArrayNodeDefinition $builder)
    {
        $builder->
            children()->
                arrayNode('table_map')->
                    useAttributeAsKey('key')->
                    prototype('variable')->
                end()->
            end();
    }

    /**
     * {@inheritdoc}
     */
    public function initialize(ExtensionManager $extensionManager)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function load(ContainerBuilder $container, array $config)
    {
        // Track our configuration on the container.
        $container->setParameter('table.parameters', $config);

        // Define the environment container service.
        $definition = new Definition(EnvironmentContainer::class);
        $container->setDefinition('table_extension.environment_container', $definition);

        // Define the event listener that captures the Behat test environment and stores it in the container.
        $definition = new Definition(EnvironmentListener::class, [
            new Reference('table_extension.environment_container'),
        ]);
        $definition->addTag(EventDispatcherExtension::SUBSCRIBER_TAG, array('priority' => 0));
        $container->setDefinition('table_extension.environment_listener', $definition);

        // Define the context initializer.
        $definition = new Definition(TableAwareInitializer::class, [
            new Reference('hook.dispatcher'),
            new Reference('table_extension.environment_container'),
            '%table.parameters%',
        ]);
        $definition->addTag(ContextExtension::INITIALIZER_TAG, array('priority' => 0));
        $container->setDefinition('table_extension.context_initializer', $definition);

        // Define the hook annotation reader.
        $definition = new Definition(HookAnnotationReader::class);
        $definition->addTag(ContextExtension::ANNOTATION_READER_TAG);
        $container->setDefinition('table_extension.hook_annotation_reader', $definition);
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigKey()
    {
        return self::CONFIG_KEY;
    }
}
