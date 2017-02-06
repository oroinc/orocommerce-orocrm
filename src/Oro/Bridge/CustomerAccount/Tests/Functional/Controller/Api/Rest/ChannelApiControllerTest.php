<?php

namespace Oro\Bridge\CustomerAccount\Tests\Functional\Controller\Api\Rest;

use Oro\Bundle\ChannelBundle\Entity\Channel;
use Oro\Bundle\ChannelBundle\Tests\Functional\Controller\Api\Rest\ChannelApiControllerTest
    as BaseChannelApiControllerTest;

class ChannelApiControllerTest extends BaseChannelApiControllerTest
{
    protected function getExpectedCountForCget()
    {
        return 3;
    }

    protected function assertActiveChannels($channels)
    {
        /** @var Channel $activeChannel */
        $activeChannel = $this->getReference('channel_1');

        $this->assertNotEmpty($channels);
        $this->assertCount(2, $channels);
        $this->assertEquals($channels[0]['name'], 'Commerce channel');
        $this->assertEquals($channels[1]['name'], $activeChannel->getName());
    }
}
