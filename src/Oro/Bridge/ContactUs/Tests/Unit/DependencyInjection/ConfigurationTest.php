<?php

namespace Oro\Bridge\ContactUs\Tests\Unit\DependencyInjection;

use Oro\Bridge\ContactUs\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\Processor;

class ConfigurationTest extends \PHPUnit\Framework\TestCase
{
    public function testGetConfigTreeBuilder()
    {
        $configuration = new Configuration();
        $builder = $configuration->getConfigTreeBuilder();
        $this->assertInstanceOf('Symfony\Component\Config\Definition\Builder\TreeBuilder', $builder);

        $root = $builder->buildTree();
        $this->assertInstanceOf('Symfony\Component\Config\Definition\ArrayNode', $root);
        $this->assertEquals('oro_contact_us_bridge', $root->getName());
    }

    public function testProcessConfiguration()
    {
        $configuration = new Configuration();
        $processor     = new Processor();

        $expected =  [
            'settings' => [
                'resolved'  => true,
                'enable_contact_request' => [
                    'value' => true,
                    'scope' => 'app'
                ],
                'consent_contact_reason' => [
                    'value' => null,
                    'scope' => 'app'
                ]
            ]
        ];

        $this->assertEquals($expected, $processor->processConfiguration($configuration, []));
    }
}
