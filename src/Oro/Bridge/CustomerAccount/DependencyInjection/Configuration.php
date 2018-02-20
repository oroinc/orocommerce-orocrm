<?php

namespace Oro\Bridge\CustomerAccount\DependencyInjection;

use Oro\Bundle\ConfigBundle\DependencyInjection\SettingsBuilder;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    const DEFAULT_CREATE_METHOD = 'each';
    const DEFAULT_SECTION_NAME = 'Commerce Customers';

    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();

        $rootNode = $treeBuilder->root('oro_customer_account_bridge');

        SettingsBuilder::append(
            $rootNode,
            [
                'customer_account_settings' => ['value' => self::DEFAULT_CREATE_METHOD, 'type' => 'scalar'],
                'commerce_customers_section_name' => ['value' => self::DEFAULT_SECTION_NAME, 'type' => 'scalar'],
            ]
        );

        return $treeBuilder;
    }
}
