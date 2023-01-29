<?php

namespace Oro\Bridge\CustomerAccount\EventListener;

use Doctrine\Persistence\Event\LifecycleEventArgs;
use Oro\Bundle\CustomerBundle\Entity\Customer;

/**
 * Listens to Customer Entity events and updates DataChannel
 */
class CustomerCreateListener
{
    const COMMERCE_CHANNEL_TYPE = 'commerce';

    public function postPersist(Customer $customer, LifecycleEventArgs $args)
    {
        $this->updateDataChannel($customer, $args);
    }

    public function postUpdate(Customer $customer, LifecycleEventArgs $args)
    {
        $this->updateDataChannel($customer, $args);
    }

    private function updateDataChannel(Customer $customer, LifecycleEventArgs $args)
    {
        $em = $args->getObjectManager();

        if (!$customer->getDataChannel()) {
            $channels = $em->getRepository('OroChannelBundle:Channel')
                ->findBy(['channelType' => self::COMMERCE_CHANNEL_TYPE]);
            if (count($channels) === 1) {
                $customer->setDataChannel(reset($channels));
            }
        }
    }
}
