<?php

declare(strict_types = 1);

namespace OpenEuropa\TableExtension\ServiceContainer;

use Behat\Behat\Context\ServiceContainer\ContextExtension;
use Behat\Testwork\ServiceContainer\Extension as ExtensionInterface;
use Behat\Testwork\ServiceContainer\ExtensionManager;
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
        $container->setParameter('table.parameters', $config);
        $definition = new Definition('OpenEuropa\TableExtension\Context\Initializer\TableAwareInitializer', [
            new Reference('hook.dispatcher'),
            '%table.parameters%',
        ]);
        $definition->addTag(ContextExtension::INITIALIZER_TAG, array('priority' => 0));
        $container->setDefinition('table.context_initializer', $definition);
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigKey()
    {
        return self::CONFIG_KEY;
    }
}
