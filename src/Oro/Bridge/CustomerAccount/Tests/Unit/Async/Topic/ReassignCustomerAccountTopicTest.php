<?php

namespace Oro\Bridge\CustomerAccount\Tests\Unit\Async\Topic;

use Oro\Bridge\CustomerAccount\Async\Topic\ReassignCustomerAccountTopic;
use Oro\Component\MessageQueue\Test\AbstractTopicTestCase;
use Oro\Component\MessageQueue\Topic\TopicInterface;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;

class ReassignCustomerAccountTopicTest extends AbstractTopicTestCase
{
    protected function getTopic(): TopicInterface
    {
        return new ReassignCustomerAccountTopic();
    }

    public function validBodyDataProvider(): array
    {
        $requiredOptionsSet = [
            'type' => 'each',
        ];

        return [
            'only required options' => [
                'body' => $requiredOptionsSet,
                'expectedBody' => $requiredOptionsSet,
            ],
        ];
    }

    /**
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function invalidBodyDataProvider(): array
    {
        return [
            'empty' => [
                'body' => [],
                'exceptionClass' => MissingOptionsException::class,
                'exceptionMessage' =>
                    '/The required option "type" is missing./',
            ],
            'wrong type of the "type" option' => [
                'body' => [
                    'type' => 1,
                ],
                'exceptionClass' => InvalidOptionsException::class,
                'exceptionMessage' => '/The option "type" with value 1 is expected to be of type "string"/',
            ],
        ];
    }

    public function testCreateJobName(): void
    {
        self::assertSame(
            'oro.customer_account.reassign_customer_account',
            $this->getTopic()->createJobName([])
        );
    }
}
