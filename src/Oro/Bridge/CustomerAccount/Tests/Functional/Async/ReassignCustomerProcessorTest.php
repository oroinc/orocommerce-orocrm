<?php

namespace Oro\Bridge\CustomerAccount\Tests\Functional\Async;

use Oro\Bridge\CustomerAccount\Async\Topic\ReassignCustomerAccountTopic;
use Oro\Bundle\CustomerBundle\Entity\Customer;
use Oro\Bundle\MessageQueueBundle\Test\Functional\MessageQueueExtension;
use Oro\Bundle\TestFrameworkBundle\Test\WebTestCase;
use Oro\Component\MessageQueue\Consumption\MessageProcessorInterface;

/**
 * @dbIsolationPerTest
 */
class ReassignCustomerProcessorTest extends WebTestCase
{
    use MessageQueueExtension;

    protected function setUp(): void
    {
        parent::setUp();

        $this->initClient();
    }

    public function testProcessStrategyNotFound(): void
    {
        $sentMessage = self::sendMessage(
            ReassignCustomerAccountTopic::getName(),
            [
                'type' => 'unknownType',
            ]
        );
        self::consumeMessage($sentMessage);

        self::assertProcessedMessageStatus(MessageProcessorInterface::REJECT, $sentMessage);
        self::assertProcessedMessageProcessor(
            'oro_customer_account.async.reassign_customer_processor',
            $sentMessage
        );
        self::assertTrue(
            self::getLoggerTestHandler()->hasError('Failed get strategy to create customer accounts. unknownType')
        );
    }

    public function testProcess(): void
    {
        $customer = self::getContainer()->get('doctrine')
            ->getRepository(Customer::class)
            ->findOneByName('CustomerUser CustomerUser');

        self::assertNull($customer->getPreviousAccount());

        $sentMessage = self::sendMessage(
            ReassignCustomerAccountTopic::getName(),
            [
                'type' => 'root',
            ]
        );
        self::consumeMessage($sentMessage);

        self::assertProcessedMessageStatus(MessageProcessorInterface::ACK, $sentMessage);
        self::assertProcessedMessageProcessor(
            'oro_customer_account.async.reassign_customer_processor',
            $sentMessage
        );

        self::assertNotNull($customer->getPreviousAccount());
    }
}
