<?php

namespace Oro\Bridge\ContactUs\DependencyInjection;

use Oro\Bundle\ConfigBundle\Config\ConfigManager;
use Oro\Bundle\ConfigBundle\DependencyInjection\SettingsBuilder;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    const ROOT_NODE = 'oro_contact_us_bridge';
    const ENABLE_CONTACT_REQUEST = 'enable_contact_request';
    const CONSENT_CONTACT_REASON = 'consent_contact_reason';

    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder(self::ROOT_NODE);

        $rootNode = $treeBuilder->getRootNode();

        SettingsBuilder::append(
            $rootNode,
            [
                self::ENABLE_CONTACT_REQUEST => ['type' => 'boolean', 'value' => true],
                self::CONSENT_CONTACT_REASON => ['value' => null, 'type' => 'integer'],
            ]
        );

        return $treeBuilder;
    }

    /**
     * @param string $key
     * @param string $separator
     * @return string
     */
    public static function getConfigKey($key, $separator = ConfigManager::SECTION_MODEL_SEPARATOR)
    {
        return sprintf('%s%s%s', self::ROOT_NODE, $separator, $key);
    }
}
