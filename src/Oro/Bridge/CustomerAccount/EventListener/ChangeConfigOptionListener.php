<?php

namespace Oro\Bridge\CustomerAccount\EventListener;

use Oro\Bridge\CustomerAccount\Async\Topics;
use Oro\Bundle\ConfigBundle\Event\ConfigUpdateEvent;
use Oro\Component\MessageQueue\Client\MessageProducerInterface;

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
            $this->producer->send(Topics::REASSIGN_CUSTOMER_ACCOUNT, ['type' => $newValue]);
        }
    }
}
