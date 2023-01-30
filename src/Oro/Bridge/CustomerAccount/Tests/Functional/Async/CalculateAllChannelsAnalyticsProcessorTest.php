<?php

namespace Oro\Bridge\CustomerAccount\Tests\Functional\Async;

use Oro\Bundle\AnalyticsBundle\Async\CalculateAllChannelsAnalyticsProcessor;
use Oro\Bundle\AnalyticsBundle\Async\Topic\CalculateChannelAnalyticsTopic;
use Oro\Bundle\AnalyticsBundle\Tests\Functional\Async\CalculateAllChannelsAnalyticsProcessorTest
as BaseCalculateAllChannelsAnalyticsProcessorTest;
use Oro\Bundle\ChannelBundle\Entity\Channel;
use Oro\Component\MessageQueue\Transport\ConnectionInterface;
use Oro\Component\MessageQueue\Transport\Message;

/**
 * @dbIsolationPerTest
 */
class CalculateAllChannelsAnalyticsProcessorTest extends BaseCalculateAllChannelsAnalyticsProcessorTest
{
    public function testShouldSendCalculateAnalyticsMessageForEachChannel(): void
    {
        /** @var CalculateAllChannelsAnalyticsProcessor $processor */
        $processor = self::getContainer()->get('oro_analytics.async.calculate_all_channels_analytics_processor');
        /** @var ConnectionInterface $connection */
        $connection = self::getContainer()->get('oro_message_queue.transport.connection');

        $processor->process(new Message(), $connection->createSession());

        self::assertMessagesCount(CalculateChannelAnalyticsTopic::getName(), 3);
    }

    public function testShouldSendCalculateAnalyticsMessageOnlyForActiveChannels(): void
    {
        /** @var Channel $channel */
        $channel = $this->getReference('Channel.CustomerChannel');
        $channel->setStatus(Channel::STATUS_INACTIVE);

        $this->getEntityManager()->persist($channel);
        $this->getEntityManager()->flush();

        /** @var CalculateAllChannelsAnalyticsProcessor $processor */
        $processor = self::getContainer()->get('oro_analytics.async.calculate_all_channels_analytics_processor');
        /** @var ConnectionInterface $connection */
        $connection = self::getContainer()->get('oro_message_queue.transport.connection');

        $processor->process(new Message(), $connection->createSession());

        self::assertMessagesCount(CalculateChannelAnalyticsTopic::getName(), 2);
    }
}
