<?php

namespace Oro\Bridge\CustomerAccount\Async\Topic;

use Oro\Component\MessageQueue\Topic\AbstractTopic;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * A topic to reassign customer account
 */
class ReassignCustomerAccountTopic extends AbstractTopic
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
}
