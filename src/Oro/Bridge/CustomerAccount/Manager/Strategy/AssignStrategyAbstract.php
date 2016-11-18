<?php

namespace Oro\Bridge\CustomerAccount\Manager\Strategy;

use Oro\Bridge\CustomerAccount\Manager\AccountBuilder;
use Oro\Bundle\CustomerBundle\Entity\Account as Customer;

abstract class AssignStrategyAbstract implements AssignStrategyInterface
{
    /**
     * @var AccountBuilder
     */
    protected $builder;

    /**
     * @param AccountBuilder $builder
     */
    public function __construct(AccountBuilder $builder)
    {
        $this->builder = $builder;
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
