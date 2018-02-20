<?php

namespace Oro\Bridge\CustomerAccount\Tests\Functional\Async;

use Oro\Bundle\AnalyticsBundle\Async\CalculateAllChannelsAnalyticsProcessor;
use Oro\Bundle\AnalyticsBundle\Async\Topics;
use Oro\Bundle\AnalyticsBundle\Tests\Functional\Async\CalculateAllChannelsAnalyticsProcessorTest
    as BaseCalculateAllChannelsAnalyticsProcessorTest;
use Oro\Bundle\ChannelBundle\Entity\Channel;
use Oro\Component\MessageQueue\Transport\Null\NullMessage;
use Oro\Component\MessageQueue\Transport\Null\NullSession;

/**
 * @dbIsolationPerTest
 */
class CalculateAllChannelsAnalyticsProcessorTest extends BaseCalculateAllChannelsAnalyticsProcessorTest
{
    public function testShouldSendCalculateAnalyticsMessageForEachChannel()
    {
        /** @var CalculateAllChannelsAnalyticsProcessor $processor */
        $processor = $this->getContainer()->get('oro_analytics.async.calculate_all_channels_analytics_processor');

        $processor->process(new NullMessage(), new NullSession());

        self::assertMessagesCount(Topics::CALCULATE_CHANNEL_ANALYTICS, 3);
    }

    public function testShouldSendCalculateAnalyticsMessageOnlyForActiveChannels()
    {
        /** @var Channel $channel */
        $channel = $this->getReference('Channel.CustomerChannel');
        $channel->setStatus(Channel::STATUS_INACTIVE);

        $this->getEntityManager()->persist($channel);
        $this->getEntityManager()->flush();

        /** @var CalculateAllChannelsAnalyticsProcessor $processor */
        $processor = $this->getContainer()->get('oro_analytics.async.calculate_all_channels_analytics_processor');

        $processor->process(new NullMessage(), new NullSession());

        self::assertMessagesCount(Topics::CALCULATE_CHANNEL_ANALYTICS, 2);
    }
}
