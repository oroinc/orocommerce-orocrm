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
    #[\Override]
    public static function getName(): string
    {
        return 'oro.customer_account.reassign_customer_account';
    }

    #[\Override]
    public static function getDescription(): string
    {
        return 'Reassigns customer account.';
    }

    #[\Override]
    public function configureMessageBody(OptionsResolver $resolver): void
    {
        $resolver
            ->setRequired('type')
            ->setAllowedTypes('type', 'string');
    }

    #[\Override]
    public function createJobName($messageBody): string
    {
        return self::getName();
    }
}
