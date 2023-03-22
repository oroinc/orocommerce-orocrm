<?php

namespace Oro\Bridge\CustomerAccount\Async\Topic;

use Oro\Component\MessageQueue\Topic\AbstractTopic;
use Oro\Component\MessageQueue\Topic\JobAwareTopicInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * A topic to reassign customer account
 */
class ReassignCustomerAccountTopic extends AbstractTopic implements JobAwareTopicInterface
{
    public static function getName(): string
    {
        return 'oro.customer_account.reassign_customer_account';
    }

    public static function getDescription(): string
    {
        return 'Reassigns customer account.';
    }

    public function configureMessageBody(OptionsResolver $resolver): void
    {
        $resolver
            ->setRequired('type')
            ->setAllowedTypes('type', 'string');
    }

    public function createJobName($messageBody): string
    {
        return self::getName();
    }
}
