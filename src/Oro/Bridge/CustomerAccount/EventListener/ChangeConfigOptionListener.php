<?php

namespace Oro\Bridge\CustomerAccount\EventListener;

use Oro\Bridge\CustomerAccount\Async\ReassingCustomerProducer;
use Oro\Bundle\ConfigBundle\Event\ConfigUpdateEvent;

class ChangeConfigOptionListener
{
    /** @var ReassingCustomerProducer */
    protected $producer;

    /**
     * @param ReassingCustomerProducer $producer
     */
    public function __construct(ReassingCustomerProducer $producer)
    {
        $this->producer = $producer;
    }

    /**
     * @param ConfigUpdateEvent $event
     */
    public function onConfigUpdate(ConfigUpdateEvent $event)
    {
        if (!$event->isChanged('oro_customer_account_bridge.customer_account_settings')) {
            return;
        }
        $newValue = $event->getNewValue('oro_customer_account_bridge.customer_account_settings');
        $oldValue = $event->getOldValue('oro_customer_account_bridge.customer_account_settings');
        if ($newValue !== $oldValue) {
            $this->producer->produce($newValue);
        }
    }
}
