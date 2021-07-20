<?php

namespace Oro\Bridge\CustomerAccount\Async;

use Oro\Bridge\CustomerAccount\Manager\AccountManager;
use Oro\Component\MessageQueue\Client\TopicSubscriberInterface;
use Oro\Component\MessageQueue\Consumption\MessageProcessorInterface;
use Oro\Component\MessageQueue\Job\JobRunner;
use Oro\Component\MessageQueue\Transport\MessageInterface;
use Oro\Component\MessageQueue\Transport\SessionInterface;
use Oro\Component\MessageQueue\Util\JSON;
use Psr\Log\LoggerInterface;

class ReassingCustomerProcessor implements MessageProcessorInterface, TopicSubscriberInterface
{
    /** @var LoggerInterface */
    private $logger;

    /** @var AccountManager */
    private $manager;

    /** @var JobRunner */
    private $jobRunner;

    public function __construct(AccountManager $manager, JobRunner $jobRunner, LoggerInterface $logger)
    {
        $this->manager = $manager;
        $this->jobRunner = $jobRunner;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function process(MessageInterface $message, SessionInterface $session)
    {
        $data = JSON::decode($message->getBody());
        $type = null;
        if (isset($data['type'])) {
            $type = $data['type'];
        }
        if (null === $type) {
            $this->logger->critical('Got invalid message');

            return self::REJECT;
        }

        $result = $this->jobRunner->runUnique(
            $message->getMessageId(),
            Topics::REASSIGN_CUSTOMER_ACCOUNT,
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
        return [Topics::REASSIGN_CUSTOMER_ACCOUNT];
    }
}
