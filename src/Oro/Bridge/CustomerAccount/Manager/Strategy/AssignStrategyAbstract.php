<?php

namespace Oro\Bridge\CustomerAccount\Manager\Strategy;

use Oro\Bridge\CustomerAccount\Manager\AccountBuilder;
use Oro\Bundle\SalesBundle\Entity\Manager\AccountCustomerManager;
use Oro\Bundle\CustomerBundle\Entity\Account as Customer;

abstract class AssignStrategyAbstract implements AssignStrategyInterface
{
    /**
     * @var AccountBuilder
     */
    protected $builder;

    /**
     * @var AccountCustomerManager
     */
    protected $accountCustomerManager;

    /**
     * @param AccountBuilder         $builder
     * @param AccountCustomerManager $accountCustomerManager
     */
    public function __construct(AccountBuilder $builder, AccountCustomerManager $accountCustomerManager)
    {
        $this->builder = $builder;
        $this->accountCustomerManager = $accountCustomerManager;
    }

    /**
     * Get root customer
     *
     * @param Customer $customer
     *
     * @return Customer
     */
    protected function getRootCustomer(Customer $customer)
    {
        if ($customer->getParent()) {
            $customer = $this->getRootCustomer($customer->getParent());
        }

        return $customer;
    }
}
