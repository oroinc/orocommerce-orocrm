<?php

namespace Oro\Bridge\CustomerAccount\Tests\Unit\Async;

use Oro\Bridge\CustomerAccount\Async\ReassingCustomerProcessor;
use Oro\Bridge\CustomerAccount\Async\Topic\ReassignCustomerAccountTopic;
use Oro\Bridge\CustomerAccount\Manager\AccountManager;
use Oro\Component\MessageQueue\Consumption\MessageProcessorInterface;
use Oro\Component\MessageQueue\Job\JobRunner;
use Oro\Component\MessageQueue\Transport\Message;
use Oro\Component\MessageQueue\Transport\SessionInterface;
use Psr\Log\LoggerInterface;

class ReassignCustomerProcessorTest extends \PHPUnit\Framework\TestCase
{
    public function testShouldSetRunUniqueJobIfTypeIsset(): void
    {
        $jobRunner = $this->createMock(JobRunner::class);
        $jobRunner
            ->expects(self::once())
            ->method('runUnique')
            ->with('message-id', ReassignCustomerAccountTopic::getName())
            ->willReturnCallback(function ($ownerId, $name, $callback) use ($jobRunner) {
                $callback($jobRunner);

                return true;
            });

        $message = new Message();
        $message->setMessageId('message-id');
        $message->setBody(['type' => 'each']);

        $processor = new ReassingCustomerProcessor(
            $this->createMock(AccountManager::class),
            $jobRunner,
            $this->createMock(LoggerInterface::class)
        );
        $result = $processor->process($message, $this->createMock(SessionInterface::class));

        self::assertEquals(MessageProcessorInterface::ACK, $result);
    }

    public function testShouldReturnSubscribedTopics(): void
    {
        self::assertEquals(
            [ReassignCustomerAccountTopic::getName()],
            ReassingCustomerProcessor::getSubscribedTopics()
        );
    }
}
