<?php

namespace Oro\Bridge\CustomerAccount\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class OroCustomerAccountBridgeExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
        $loader->load('commands.yml');
        $loader->load('controllers.yml');
        $loader->load('mq_topics.yml');
        $loader->load('mq_processors.yml');

        $container->prependExtensionConfig($this->getAlias(), $config);
    }

    /**
     * {@inheritDoc}
     */
    public function getAlias()
    {
        return 'oro_customer_account_bridge';
    }
}
