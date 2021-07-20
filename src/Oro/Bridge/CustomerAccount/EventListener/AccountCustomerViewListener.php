<?php

namespace Oro\Bridge\CustomerAccount\EventListener;

use Oro\Bundle\CustomerBundle\Entity\Customer;
use Oro\Bundle\SalesBundle\Entity\Manager\AccountCustomerManager;
use Oro\Bundle\UIBundle\Event\BeforeListRenderEvent;

/**
 * Listener adds account information to commerce customer page.
 */
class AccountCustomerViewListener
{
    /**
     * @var AccountCustomerManager
     */
    protected $accountCustomerManager;

    public function __construct(AccountCustomerManager $accountCustomerManager)
    {
        $this->accountCustomerManager = $accountCustomerManager;
    }

    public function onView(BeforeListRenderEvent $event)
    {
        /** @var Customer $customer */
        $customer = $event->getEntity();
        if (!$customer instanceof Customer) {
            return;
        }

        $salesCustomer = $this->accountCustomerManager->getAccountCustomerByTarget($customer, false);
        $template = $event->getEnvironment()->render(
            '@OroCustomerAccountBridge/Customer/accountView.html.twig',
            ['salesCustomer' => $salesCustomer]
        );
        $event->getScrollData()->addSubBlockData(0, 0, $template);
    }
}
