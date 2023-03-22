<?php

namespace Oro\Bridge\CustomerAccount\Async;

use Oro\Bridge\CustomerAccount\Async\Topic\ReassignCustomerAccountTopic;
use Oro\Bridge\CustomerAccount\Manager\AccountManager;
use Oro\Component\MessageQueue\Client\TopicSubscriberInterface;
use Oro\Component\MessageQueue\Consumption\MessageProcessorInterface;
use Oro\Component\MessageQueue\Job\JobRunner;
use Oro\Component\MessageQueue\Transport\MessageInterface;
use Oro\Component\MessageQueue\Transport\SessionInterface;

/**
 * Reassigns customer account
 */
class ReassignCustomerProcessor implements MessageProcessorInterface, TopicSubscriberInterface
{
    /** @var AccountManager */
    private $manager;

    /** @var JobRunner */
    private $jobRunner;

    public function __construct(AccountManager $manager, JobRunner $jobRunner)
    {
        $this->manager = $manager;
        $this->jobRunner = $jobRunner;
    }

    /**
     * {@inheritdoc}
     */
    public function process(MessageInterface $message, SessionInterface $session)
    {
        $messageBody = $message->getBody();
        $type = $messageBody['type'];
        $result = $this->jobRunner->runUniqueByMessage(
            $message,
            function () use ($type) {
                return $this->manager->assignAccounts($type);
            }
        );

        return $result ? self::ACK : self::REJECT;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedTopics()
    {
        return [ReassignCustomerAccountTopic::getName()];
    }
}
