<?php

namespace Oro\Bridge\ContactUs\Tests\Unit\DependencyInjection;

use Oro\Bridge\ContactUs\DependencyInjection\OroContactUsBridgeExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class OroContactUsBridgeExtensionTest extends \PHPUnit\Framework\TestCase
{
    public function testLoad(): void
    {
        $container = new ContainerBuilder();

        $extension = new OroContactUsBridgeExtension();
        $extension->load([], $container);

        self::assertNotEmpty($container->getDefinitions());
        self::assertSame(
            [
                [
                    'settings' => [
                        'resolved' => true,
                        'enable_contact_request' => ['value' => true, 'scope' => 'app'],
                        'consent_contact_reason' => ['value' => null, 'scope' => 'app'],
                    ]
                ]
            ],
            $container->getExtensionConfig('oro_contact_us_bridge')
        );
    }
}
