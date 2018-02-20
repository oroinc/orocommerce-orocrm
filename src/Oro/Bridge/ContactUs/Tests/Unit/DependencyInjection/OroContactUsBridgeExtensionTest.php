<?php

namespace Oro\Bridge\ContactUs\Tests\Unit\DependencyInjection;

use Oro\Bridge\ContactUs\DependencyInjection\OroContactUsBridgeExtension;
use Oro\Bundle\TestFrameworkBundle\Test\DependencyInjection\ExtensionTestCase;

class OroContactUsBridgeExtensionTest extends ExtensionTestCase
{
    public function testLoad()
    {
        $this->loadExtension(new OroContactUsBridgeExtension());

        $expectedServices = [
            'oro_contact_us_bridge.contact_request_type',
        ];
        $this->assertDefinitionsLoaded($expectedServices);
    }

    public function testGetAlias()
    {
        $extension = new OroContactUsBridgeExtension();
        $this->assertSame('oro_contact_us_bridge', $extension->getAlias());
    }
}
