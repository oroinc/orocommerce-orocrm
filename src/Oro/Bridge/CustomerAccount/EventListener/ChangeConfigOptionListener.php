<?php

namespace Oro\Bridge\CustomerAccount\EventListener;

use Oro\Bridge\CustomerAccount\Async\Topic\ReassignCustomerAccountTopic;
use Oro\Bundle\ConfigBundle\Event\ConfigUpdateEvent;
use Oro\Component\MessageQueue\Client\MessageProducerInterface;

/**
 * Sends a {@see ReassignCustomerAccountTopic::getName()} message with
 * new value of the "oro_customer_account_bridge.customer_account_settings" config option to MQ for processing
 */
class ChangeConfigOptionListener
{
    /** @var MessageProducerInterface */
    protected $producer;

    public function __construct(MessageProducerInterface $producer)
    {
        $this->producer = $producer;
    }

    public function onConfigUpdate(ConfigUpdateEvent $event)
    {
        if (!$event->isChanged('oro_customer_account_bridge.customer_account_settings')) {
            return;
        }

        $newValue = $event->getNewValue('oro_customer_account_bridge.customer_account_settings');
        $oldValue = $event->getOldValue('oro_customer_account_bridge.customer_account_settings');

        if ($newValue !== $oldValue) {
            $this->producer->send(ReassignCustomerAccountTopic::getName(), ['type' => $newValue]);
        }
    }
}
