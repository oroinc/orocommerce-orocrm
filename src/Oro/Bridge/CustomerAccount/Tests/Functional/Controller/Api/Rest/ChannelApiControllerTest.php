<?php

namespace Oro\Bridge\CustomerAccount\Tests\Functional\Controller\Api\Rest;

use Oro\Bundle\ChannelBundle\Entity\Channel;
use Oro\Bundle\ChannelBundle\Tests\Functional\Controller\Api\Rest\ChannelApiControllerTest as BaseControllerTest;

class ChannelApiControllerTest extends BaseControllerTest
{
    /**
     * {@inheritDoc}
     */
    protected function getExpectedCountForCget(): int
    {
        return 3;
    }

    /**
     * {@inheritDoc}
     */
    protected function assertActiveChannels(array $channels): void
    {
        /** @var Channel $activeChannel */
        $activeChannel = $this->getReference('channel_1');

        $this->assertNotEmpty($channels);
        $this->assertCount(2, $channels);
        $this->assertEquals('Commerce channel', $channels[0]['name']);
        $this->assertEquals($activeChannel->getName(), $channels[1]['name']);
    }
}
